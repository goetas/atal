<?php
namespace goetas\atal;
use DOMException;
use DOMText;
use DOMCdataSection;
use DOMNode;
class Compiler extends BaseClass{
	
	const NS = ATal::NS;

	/**
	 * @var ATal
	 */
	protected $tal;
	/**
	 * @var string
	 */
	protected $template;
	
	/**
	 * Espressione regolare per trovare le variabili racchiuse tra parentresi graffe
	 * @var string
	 */
	protected $currRegex;
	/**
	 * 
	 * @var loaders\Attributes
	 */	
	protected $attributes;
	/**
	 * 
	 * @var loaders\Selectors 
	 */		
	protected $selectors;
	
	/**
	 * 
	 * @var filters\XmlFilter
	 */
	protected $preXmlFilters;
	/**
	 * 
	 * @var filters\XmlFilter
	 */	
	protected $postXmlFilters;
	/**
	 * 
	 * @var filters\DomFilter
	 */	
	protected $postApplyTemplatesFilters;
	
	
	/**
	 * 
	 * @var filters\StringFilter
	 */	
	protected $preFilters;
	/**
	 * 
	 * @var filters\StringFilter
	 */	
	protected $postFilters;
	/**
	 * 
	 * @var filters\StringFilter
	 */	
	protected $postLoadFilters;
	/**
	 * Crea un compilatore per il template $atal.
	 * @param \goetas\atal\ATal $tal
	 */
	function __construct(ATal $tal) {
		$this->tal = $tal;
		
		$this->currRegex = '/\\{([\'a-z\$\\\\].*?)\\}/';
		
		$this->attributes = new loaders\Attributes ($this->tal,$this);
		$this->selectors = new loaders\Selectors ($this->tal,$this);
				
		$this->preXmlFilters = new filters\XmlFilter ($this);
		$this->postXmlFilters = new filters\XmlFilter ($this);
		$this->postApplyTemplatesFilters = new filters\DomFilter ($this);
		
		$this->postLoadFilters = new filters\StringFilter ($this);
		
		$this->preFilters = new filters\StringFilter ($this);
		$this->postFilters = new filters\StringFilter ($this);
		
		$this->postFilters->addFilter(array($this,'_replaceAttributeVars'));
		$this->postFilters->addFilter(array($this,'_replaceTextVars'));
		
	}
	/**
	 * @return \goetas\atal\ATal
	 */
	function getATal() {
		return $this->tal;
	}
	/**
	 * Sostituisci i caratteri per gli attributi
	 * @param string $string
	 * @return string
	 */
	public function _replaceAttributeVars($string) {
		return preg_replace ( "/" . preg_quote ( "[#tal_attr#", "/" ) . "(" . preg_quote ( '$', "/" ) . "[a-zA-Z_\x7f-\xff][a-zA-Z0-9_\x7f-\xff]*)" . preg_quote ( "#tal_attr#]", "/" ) . "/", "<?php print( \\1 ) ?>", $string );
	}
	/**
	 * @return \goetas\atal\loaders\Attributes
	 */
	public function getAttributes() {
		return $this->attributes;
	}

	/**
	 * @return \goetas\atal\loaders\Selectors
	 */
	public function getSelectors() {
		return $this->selectors;
	}
	/**
	 * @return \goetas\atal\filters\StringFilter
	 */
	public function getPostLoadFilters() {
		return $this->postLoadFilters;
	}
	/**
	 * @return \goetas\atal\filters\XmlFilter
	 */
	public function getPreXmlFilters() {
		return $this->preXmlFilters;
	}

	/**
	 * @return \goetas\atal\filters\StringFilter
	 */
	public function getPostXmlFilters() {
		return $this->postXmlFilters;
	}
	/**
	 * @return the $postApplyTemplatesFilters
	 */
	public function getPostApplyTemplatesFilters() {
		return $this->postApplyTemplatesFilters;
	}

	/**
	 * @return \goetas\atal\filters\StringFilter
	 */
	public function getPreFilters() {
		return $this->preFilters;
	}

	/**
	 * @return \goetas\atal\filters\StringFilter
	 */
	public function getPostFilters() {
		return $this->postFilters;
	}
	function __clone() {
	}
	/**
	 * Ritorna il nome del template corrente
	 * @return string
	 */
	public function getTemplate() {
		return $this->template;
	}
	/**
	 * Ritorna la regex corrente per estrarre le variabili "inline"
	 * @return string $currRegex
	 */
	public function getCurrRegex() {
		return $this->currRegex;
	}

	/**
	 * Imposta la regex corrente per estrarre le variabili "inline"
	 * @param string $currRegex the $currRegex to set
	 */
	public function setCurrRegex($currRegex) {
		$this->currRegex = $currRegex;
	}
	/**
	 * Ritorna la rappresentazione DOM di $tpl e esegui il filtro di tipo "selettore".
	 * Applica anche i filtri postLoad sul file
	 * @param string $tpl
	 * @param string $tipo
	 * @param string $query
	 * @return xml\XMLDom
	 */
	protected function toDom($tpl, $tipo, $query) {
		
		$xmlString = file_get_contents ( $tpl );
		$xmlString = $this->getPostLoadFilters()->applyFilters($xmlString);

		$tplDom = new xml\XMLDom ();
		$tplDom->loadXML ( $xmlString );
		
		$nodes = array();
		$dtd = null;
		try {
			if ($tipo) {
				$selector = $this->getSelectors()->selector($tipo);
				$selector->setDom($tplDom);
							
				$res = $selector->select ( $query );
				foreach ( $res as $node ) {
					$nodes[]=$node ;
				}
			} else {
				$dtd = $tplDom->doctype;
				$nodes[] = $tplDom->documentElement;
			}
		} catch ( \Exception $e ) {
			throw $e;
		}
		
		if(!$dtd){
			$tplDom = new xml\XMLDom ();
		}

		$root = $tplDom->addChildNS ( self::NS, "atal-content" );	
			
		foreach ( $nodes as $node ) {
			$root->appendChild ($tplDom->importNode ( $node, true));
		}	
			
		return $tplDom;
	}
	public function getExtensionTemplate(xml\XMLDom $xml) {
		
		$res = $xml->query ( "/t:atal-content/*/@t:extends", array ("t" => self::NS ) );
		
		if($res->length){
			$cw = getcwd();
			chdir(dirname($this->template));
			$rp = realpath($res->item(0)->value);
			chdir($cw);
			return $rp;
		}
		return null;
	}
	/**
	 * Ritorna una stringa del DOM presente in $xml
	 * @param $xml
	 */
	protected function serializeXml($tpl, $tipo, $query, $templateName, $baseTemplate, xml\XMLDom $xml) {
		
		foreach ( $xml->query ( "//processing-instruction()" ) as $node ) {
			if($node->parentNode && $node->parentNode->namespaceURI!=self::NS){
				$new = $xml->createTextNode("\n");
				if($node->nextSibling){
					$node->parentNode->insertBefore($new,$node->nextSibling);
				}else{
					$node->parentNode->appendChild($new);
				}
			}
		}
		
		$cnt = array();
		
		$className = $this->tal->getClassFromParts($tpl, $tipo, $query);
		
		$cnt[] = "<?php\n";
		$cnt[] = "//$templateName -- $baseTemplate\n";
		
		if($baseTemplate){
			$cnt[] = "require_once '".addcslashes($baseTemplate, "\\")."'; \n";
			$baseClasName = $this->tal->getClassFromPath($baseTemplate);
		}else{
			$baseClasName = '\\goetas\\atal\\Template';	
		}

		
		
		$cnt[] = "class $className extends $baseClasName{\n";
		if(!$baseTemplate){
			$cnt[] = "function display(){\n";
			$cnt[] = "extract(\$this->getData());?> ";
			
			if ($this->tal->xmlDeclaration) {
				$cnt []= '<?xml version="1.0" encoding="utf-8"?>' . "\n";
			}
			if ($xml->doctype) {
				$cnt []= $xml->saveXML ( $xml->doctype ) . "\n";
			}
			// mettendo queste 2 query xpath insieme il php genera i nodi in ordine sbagliato
			foreach ( $xml->query ( "/processing-instruction()" ) as $node ) {
				$cnt []= $xml->saveXML ( $node );
			}
			foreach ( $xml->query ( "/t:atal-content/node()|/text()", array ("t" => self::NS ) ) as $node ) {
				if($node->namespaceURI!=self::NS){
					$cnt []= $xml->saveXML ( $node );
				}
			}
			// fine bug
			
			$cnt[] = "<?php\t}\n ";
		}		
		foreach ( $xml->query ( "/t:atal-content/t:atal-block", array ("t" => self::NS ) ) as $node ) {
			$tcnt = '';
			foreach ($node->childNodes as $cn){
				$tcnt .= $xml->saveXML ( $cn );
			}
			if(substr($tcnt, 0, 5)=='<?php' && substr($tcnt, -2)=='?>'){
				$cnt []= substr($tcnt, 5, -2);
			}else{
				throw new \Exception("errore atal block");
			}
			
		}
		$cnt[] = "}"; // fine classe
		$cnt = implode("",$cnt);
		
		return $cnt;
		
	}
	/**
	 * Compila un template e salvalo in $destination
	 * @param string $tpl
	 * @param string $tipo
	 * @param string $query
	 * @param string $destination
	 */
	public function compile($tpl, $tipo, $query, $destination) {
		$this->template = $tpl;
		
		$xml  = $this->toDom ( $tpl, $tipo, $query );
		
		$xml = $this->getPreXmlFilters()->applyFilters($xml);	
		
		$baseTemplate = $this->getExtensionTemplate($xml);

		$parents = array();
		if (!$baseTemplate){
						
		}else{
			try{
				$cw = getcwd();
				chdir(dirname($this->template));
							
				list ($tpl2, $tipo2, $query2) = $this->tal->parseUriParts($baseTemplate); 
				
				$destination2 = $this->tal->getCacheName($baseTemplate);
				
				$this->compile($tpl2, $tipo2, $query2, $destination2);
				
				chdir($cw);
			}catch(\Exception $e){
				chdir($cw);
				throw $e;
			}
			
			$parents = $this->findDefBlocks($xml->documentElement);
			
		}
		
		$this->findBlocks($xml->documentElement);
		
		$this->applyTemplates ( $xml->documentElement );
		$this->getPostApplyTemplatesFilters()->applyFilters($xml);
		//foreach ($parents as $nd){
			//$nd->remove();
		//}
		
		$xml = $this->getPostXmlFilters()->applyFilters($xml);
	
		$cnt = $this->serializeXml ( $tpl, $tipo, $query, $destination, $destination2, $xml );
		//echo "\n--cnt---$tpl----\n".$cnt."\n";
		$cnt = $this->getPostFilters()->applyFilters($cnt);
		//if($destination2) die($cnt);
		if(file_put_contents ( $destination.".tmp", $cnt )){
			rename ( $destination.".tmp", $destination );
		}
	}
	
	public function findDefBlocks(xml\XMLDomElement $node) {

		$res = $node->query ( "/t:atal-content/*[@t:extends]/*", array ("t" => self::NS ) );

		$parents = array();
		$nomi = array();
		foreach ($res as $blocco ) {	
			
			$blockName = $blocco->getAttributeNs(self::NS, "block");
			if(!$blockName){
				throw new Exception("Tutti gli elementi di figli di @extends devono essere definizioni di blocco");
			}
			if(isset($nomi[$blockName])){
				throw new Exception("Dichiarazione duplicata per il blocco '$blockName'");
			}
			$nomi[$blockName]=true;
			$blocco->setAttributeNs(self::NS, "block-redef", $blockName);
			$blocco->removeAttributeNs(self::NS, "block");
		}
	}
	public function findBlocks(xml\XMLDomElement $node) {
		$res = $node->query ( "//*[@t:block]", array ("t" => self::NS ) );
		$nomi = array();
		foreach ($res as $blocco ) {
			$blockName = $blocco->getAttributeNs(self::NS, "block");
			if(isset($nomi[$blockName])){
				throw new Exception("Dichiarazione duplicata per il blocco '$blockName'");
			}
			$nomi[$blockName]=true;
			$blocco->setAttributeNs(self::NS, "block-def", $blockName);
			$blocco->setAttributeNs(self::NS, "block-call", $blockName);
			$blocco->removeAttributeNs(self::NS, "block");
		}		
	}
	/**
	 * Esegue il parsing di espressioni e attributi di tipo ATal
	 * @param xml\XMLDomElement $node
	 * @param bool $skip
	 * @return void
	 */
	public function applyTemplates(xml\XMLDomElement $node, $skip = array()) {
		$attributes = array ();
		$talAttributes = array ();
		
		$childNodes = array ();
		
		foreach ( $node->attributes as $attr ) {
			$attributes [] = $attr;
		}
		foreach ( $node->childNodes as $child ) {
			$childNodes [] = $child;
		}
		$stopNode = 0;
		$attPluginsUsed = array ();
		foreach ( $attributes as $attr ) {
			if($attr->namespaceURI == self::NS){ // è un attributo tal
				if ($attr->localName != 'id') {
					$attPlugin = $this->attributes->attribute($attr->localName);
					$attPlugin->setDom($node->ownerDocument);
					if (! in_array ( $attr->localName, $skip ) && $attr->ownerElement === $node) {
						$attPluginsUsed [] = array ($attPlugin, $node, $attr );
						$continueRule = $attPlugin->start ( $node, $attr  );
						try {
							$node->removeAttributeNode ( $attr );
						} catch ( DOMException $e ) {					
						}
						if ($continueRule & Attribute::STOP_NODE && $continueRule & Attribute::STOP_ATTRIBUTE) {
							return;
						} elseif ($continueRule & Attribute::STOP_NODE) {
							$stopNode = 1;
						} elseif ($continueRule & Attribute::STOP_ATTRIBUTE) {
							break;
						}
					}
				}
			} else {
				$this->applyAttributeVars ( $attr );
			}
		}
		if (! $stopNode) {
			foreach ( $childNodes as $child ) {
				if ($child instanceof xml\XMLDomElement) {
					$this->applyTemplates ( $child );
				} elseif ($child instanceof DOMText) {
					$this->applyTextVars ( $child ); // applica le variabili sul testo
				}
			}
			foreach ( $attPluginsUsed as $data ) {
				if ($data [1]->ownerDocument != null) { // nodo ancora non rimosso
					$data [0]->end ( $data [1], $data [2] );
				}
			}
		}
	}
	public static function _replaceTextVars($string) {
		return str_replace(array("<![CDATA[{{__NOCDATA__", "__NOCDATA__}}]]>"), "", $string);
	}
	/**
	 * Applica {@method parsedExpression} ad un nodo DOM di tipo testo (e cdata)
	 * @param $attr
	 * @return void
	 */
	public function applyTextVars(DOMText $nodo) {
		$mch = array ();
		if (preg_match_all ( $this->currRegex, $nodo->data, $mch )) {
			$xml = $nodo->data;
			foreach ( $mch [0] as $k => $pattern ) {
				$xml = str_replace ( $pattern, '<?php print( '. $this->parsedExpression ( $mch [1] [$k] ) . '); ?>' , $xml );
			}
			if(!($nodo instanceof DOMCdataSection)){
				$xml = "{{__NOCDATA__{$xml}__NOCDATA__}}";
			}
			$newEl = $nodo->ownerDocument->createCDATASection($xml);
			
			if ($nodo->parentNode instanceof DOMNode) {
				$nodo->parentNode->replaceChild( $newEl , $nodo );
			} else {
				throw new Exception ( $nodo->nodeName . ' non ha un padre. ' );
			}
		}
	}
	public function applyAttributeVars(\DOMAttr $attr) {
		$mch = array ();
		if (preg_match_all ( $this->currRegex, $attr->value, $mch )) {
			$code = '';
			$nodo = $attr->ownerElement;
			if(!$nodo->ownerDocument){
				echo htmlentities($attr->value);
			}
			$val = $attr->value;
			
			foreach ( $mch [1] as $k => $mc ) {
				$attName = "\$__tal_attr_" . md5 ( $k . microtime () );
				$code .= "$attName  =  " . $this->parsedExpression ( $mc ) . " ;\n ";
				$val = str_replace ( $mch [0] [$k], "[#tal_attr#" . $attName . "#tal_attr#]", $val );
			}
			$attr->value = htmlspecialchars ( $val, ENT_QUOTES, 'UTF-8' );
			
			$pi = $nodo->ownerDocument->createProcessingInstruction ( "php", $code );
			$nodo->parentNode->insertBefore ( $pi, $nodo );
		}
	}	
	/**
	 * Esegue il parsing di un espressione e ci applica i relativi modificatori.
	 * @param string $exp
	 * @param bool $skip (opzionale, default=false)
	 * @return string
	 */
	public function parsedExpression($exp, $skip = false) {
		$parts = $this->splitExpression ( $exp, '|' );
		$var = trim ( array_shift ( $parts ) );
		$mch = array ();
		// usa/cerca il default modifier ??
		if ($skip) { // no

		} elseif (preg_match ( "/^([a-zA-Z_0-9]+)\\s*:\\s*(.+)/", $var, $mch )) { // cerco un pre-modifier specificato
			$parts [] = trim ( $mch [1] );
			$var = trim ( $mch [2] );
		} else{ //  usa il pre modificatre di default
			$parts [] = '';
		}
		foreach ( $parts as $part ) {
			if (preg_match ( '#(^[a-z][a-z0-9_\\-]*\s*:)#i', $part, $mch )) { // modificatore con parametri
				// modifier con parametri
				$modifierParts = $this->splitExpression ( $part, ':' );
				$modName = array_shift ( $modifierParts );

				$modParams = array();
				foreach ( $modifierParts as $modifierParam ) {
					$mch = array ();
					if (preg_match ( "/^([a-z][a-z0-9_\\-]*)\\s*\\=(.*)/i", $modifierParam, $mch )) { // parametri con nome
						$exs = trim ( $mch [2] );
						$exs = $exs == "(" && $exs [strlen ( $exs ) - 1] == ")" ? substr ( $exs, 1, - 1 ) : $exs;
						$modParams[$mch [1]]=$this->parsedExpression ( $exs, true );
					} else { // parametri numerici
						$paramStr = trim ( $modifierParam );
						$paramStr = $paramStr [0] == "(" && $paramStr [strlen ( $paramStr ) - 1] == ")" ? substr ( $paramStr, 1, - 1 ) : $paramStr;
						$modParams[] = $this->parsedExpression ( $paramStr, true );
					}
				}
				$var = "\$this->modifiers->modifier('$modName')->modify($var, " . $this->dumpKeyed($modParams)." )";
			}elseif ( $part==='' || preg_match ( '#(^[a-z][a-z0-9_\\-]*$)#i', $part )) { // modificatore senza parametri o di default
				$var = "\$this->modifiers->modifier('$part')->modify($var , array() )";		
			} else{
				throw new Exception ( "Errore di sintassi vicino a '$part'" );
			}			
		}
		return $var;
	}
	/**
	 * Divide un espressione usando $splitrer come carattere di divisione e ritorna un array con le parti di cui è composto.
	 * @param string $str
	 * @param string $splitrer
	 * @return array
	 */
	public function splitExpression($str, $splitrer){
		return static::staticSplitExpression($str, $splitrer);
	}
	public static function staticSplitExpression($str, $splitrer) {
		$str = str_split ( $str, 1 );
		$str [] = " ";
		$str_len = count ( $str );
		
		$splitrer = str_split ( $splitrer, 1 );
		$splitrer_len = count ( $splitrer );
		
		$parts = array ();
		$inApex = false;
		$next = 0;
		$pcount = 0;
		for($i = 0; $i < $str_len; $i ++) {
			if ($inApex === false && ($i === 0 || $str [$i - 1] !== "\\") && ($str [$i] === "\"" || $str [$i] === "'")) { // ingresso
				$inApex = $str [$i];
			} elseif ($inApex === $str [$i] && $str [$i - 1] !== "\\") { // uscita
				$inApex = false;
			}
			if ($inApex === false && $str [$i] === "(") {
				$pcount ++;
			} elseif ($inApex === false && $str [$i] === ")") {
				$pcount --;
			}
			if ($inApex === false && $pcount === 0 && (array_slice ( $str, $i, $splitrer_len ) == $splitrer || $i == ($str_len - 1))) {
				$val = trim ( implode ( '', array_slice ( $str, $next, $i - $next ) ) );
				if (strlen ( $val )) {
					$parts [] = $val;
				}
				$next = $i + $splitrer_len;
			}
		}
		if ($pcount != 0) {
			throw new Exception ( "Perentesi non bilanciate nell'espressione '".implode("",$str)."'" );
		}elseif ($inApex !== false) {
			throw new Exception ( "Apici non bilanciati nell'espressione '".implode("",$str)."'" );
		}
		return $parts;
	}
	public function dumpKeyed(array $parts) {
		$r = ' array(';
		foreach ( $parts as $key => $val ) {
			$r .= "'$key'=>" . $val . ", ";
		}
		return $r." ) " ;
	}
}
