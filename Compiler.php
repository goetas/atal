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
		$this->postFilters->addFilter( array(__CLASS__, "_replaceCdataVars" ) );
	}
	/**
	 * @return ATal
	 */
	function getATal() {
		return $this->tal;
	}
	public function _replaceAttributeVars($string) {
		return preg_replace ( "/" . preg_quote ( "[#tal_attr#", "/" ) . "(" . preg_quote ( '$', "/" ) . "[a-zA-Z_\x7f-\xff][a-zA-Z0-9_\x7f-\xff]*)" . preg_quote ( "#tal_attr#]", "/" ) . "/", "<?php print( \\1 ) ?>", $string );
	}
	public static function _replaceCdataVars($string) {
		return preg_replace_callback( "/" . preg_quote( '[-[?php]', '/' ) . '(.*?)' . preg_quote( '[php?]-]', '/' ) . '/', function($mch){
			return "<?php ". htmlspecialchars_decode($mch[1])  ." ?>";
		}, $string );
	}
	/**
	 * @return the $attributes
	 */
	public function getAttributes() {
		return $this->attributes;
	}

	/**
	 * @return the $selectors
	 */
	public function getSelectors() {
		return $this->selectors;
	}
	/**
	 * @return the $preXmlFilters
	 */
	public function getPostLoadFilters() {
		return $this->postLoadFilters;
	}
	/**
	 * @return the $preXmlFilters
	 */
	public function getPreXmlFilters() {
		return $this->preXmlFilters;
	}

	/**
	 * @return the $postXmlFilters
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
	 * @return the $preFilters
	 */
	public function getPreFilters() {
		return $this->preFilters;
	}

	/**
	 * @return the $postFilters
	 */
	public function getPostFilters() {
		return $this->postFilters;
	}
	function __clone() {
	}
	public function getTemplate() {
		return $this->template;
	}
	/**
	 * @return the $currRegex
	 */
	public function getCurrRegex() {
		return $this->currRegex;
	}

	/**
	 * @param $currRegex the $currRegex to set
	 */
	public function setCurrRegex($currRegex) {
		$this->currRegex = $currRegex;
	}
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
		$tplDom = $this->getPreXmlFilters()->applyFilters($tplDom);		
		return $tplDom;
	}
	protected function serializeXml(xml\XMLDom $xml) {
		$cnt = array();
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
			$cnt []= $xml->saveXML ( $node );
		}
		// fine bug
		$cnt = implode("",$cnt);
	
		return $cnt;
		
	}
	public function compile($tpl, $tipo, $query, $destination) {
		$this->template = $tpl;
		
		$xml  = $this->toDom ( $tpl, $tipo, $query );
		
		$this->applyTemplates ( $xml->documentElement );
		
		$this->getPostApplyTemplatesFilters()->applyFilters($xml);
		
		$xml = $this->getPostXmlFilters()->applyFilters($xml);
			
		$cnt = $this->serializeXml ( $xml );
		
		$cnt = $this->getPostFilters()->applyFilters($cnt);
		
		file_put_contents ( $destination, $cnt );
		
		chmod ( $destination, 0666 );

	}
	public function applyTemplates(xml\XMLDomElement $node, $skip = array()) {
		$attributes = array ();
		$talAttributes = array ();
		
		$childNodes = array ();
		
		foreach ( $node->attributes as $attr ) {
			$attributes[] = $attr;
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
	
	

	public function applyTextVars($nodo) {		
		$mch = array ();
		if ($nodo instanceof DOMText && preg_match_all ( $this->currRegex, $nodo->data, $mch )) {
			$cdata = ($nodo instanceof DOMCdataSection);
			
			if ($cdata) {
				$xml = '<![CDATA[' . $nodo->data . ']]>';
			
			} else {
				$xml = $nodo->data;
			}
			
			$tdom = new xml\XMLDom ();
			$frag = $tdom->createDocumentFragment ();
			foreach ( $mch [0] as $k => $pattern ) {
				$xml = str_replace ( $pattern, '[-[?php] echo ' . $this->parsedExpression ( $mch [1] [$k] ) . '; [php?]-]', $xml );
			}
			
			if (! $cdata) {
				$xml = htmlspecialchars($xml, ENT_NOQUOTES, 'utf-8' );
			}
			$frag->appendXML ( $xml );
			
			$tdom->appendChild ( $frag );
			
			foreach ( $tdom->childNodes as $k => $el ) {
				$nel [$k] = $nodo->ownerDocument->importNode ( $el, true );
				if ($nodo->parentNode instanceof DOMNode) {
					$nodo->parentNode->insertBefore ( $nel [$k], $nodo );
				} else {
					throw new Exception ( $nodo->nodeName . ' non ha un padre. ' . $nodo->nodeValue );
				}
				
				if ($nel [$k] instanceof xml\XMLDomElement) {
					$this->applyTemplates ( $nel [$k] );
				}
			}
			$nodo->parentNode->removeChild ( $nodo );
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
	/* compiler utils */
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
				$var = "\$__tal_modifiers->modifier('$modName')->modify($var, " . $this->dumpKeyed($modParams)." )";
			}elseif ( $part==='' || preg_match ( '#(^[a-z][a-z0-9_\\-]*$)#i', $part )) { // modificatore senza parametri o di default
				$var = "\$__tal_modifiers->modifier('$part')->modify($var , array() )";		
			} else{
				throw new Exception ( "Errore di sintassi vicino a '$part'" );
			}			
		}
		return $var;
	}
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
