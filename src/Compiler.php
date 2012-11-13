<?php
namespace goetas\atal;
use DOMException;
use DOMText;
use DOMCdataSection;
use DOMNode;
use DOMProcessingInstruction;
use goetas\xml;
class Compiler extends BaseClass{

	const NS = ATal::NS;

	/**
	 * @var ATal
	 */
	protected $tal;

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
	 *
	 * @var Template
	 */
	protected $template;

	/**
	 * Crea un compilatore per il template $atal.
	 * @param \goetas\atal\ATal $tal
	 */
	function __construct(ATal $tal, Template $template) {
		$this->tal = $tal;
		$this->setTemplate ( $template );

		$this->currRegex = '/\\{([\'a-z\$\\\\].*?)\\}/';

		$this->attributes = new loaders\Attributes ( $this->tal, $this );
		$this->selectors = new loaders\Selectors ( $this->tal, $this );

		$this->preXmlFilters = new filters\XmlFilter ( $this );
		$this->postXmlFilters = new filters\XmlFilter ( $this );
		$this->postApplyTemplatesFilters = new filters\DomFilter ( $this );

		$this->postLoadFilters = new filters\StringFilter ( $this );

		$this->preFilters = new filters\StringFilter ( $this );
		$this->postFilters = new filters\StringFilter ( $this );

		$this->postFilters->addFilter ( array ($this, '_replaceAttributeVars' ) );
		$this->postFilters->addFilter ( array ($this, '_replaceTextVars' ) );

	}

	/**
	 * @return Template
	 */
	public function getTemplate() {
		return $this->template;
	}

	/**
	 * @param Template $templateRef
	 */
	public function setTemplate(Template $template) {
		$this->template = $template;
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
		return preg_replace ( "/" . preg_quote ( "[#tal_attr#", "/" ) . "(" . preg_quote ( '$', "/" ) . "[a-zA-Z_\x7f-\xff][a-zA-Z0-9_\x7f-\xff]*)" . preg_quote ( "#tal_attr#]", "/" ) . "/", "<?php print( \\1 ) ?>\n", $string );
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
	 * @return xml\XMLDom
	 */
	protected function toDom() {



		$xmlString = $this->getPostLoadFilters ()->applyFilters ( $this->getTemplate ()->getContent() );
	
		$tplDom = new xml\XMLDom ();
		$tplDom->loadXMLStrict ( $xmlString );
		
		$ref = $this->getTemplate ()->getRef();
		
		if ($ref->getSelectorType ()) {
			try {
				
				$selector = $this->getSelectors ()->selector ( $ref->getSelectorType () );
				$selector->setDom ( $tplDom );

				$nodes = $selector->select ( $ref->getSelectorQuery () );
				
				$tplDom = new xml\XMLDom ();
				foreach ( $nodes as $node ) {
					$tplDom->appendChild ( $tplDom->importNode ( $node, true ) );
				}
					
			} catch (\Exception $e ) {
				throw $e;
			}
		}		
		return $tplDom;
	}
	protected function getExtensionTemplate(xml\XMLDom $xml) {
		$res = $xml->query ( "/*/@t:extends", array ("t" => self::NS ) );
		if ($res->length) {
			return $res->item ( 0 )->value;
		}
		return null;
	}
	protected function cleanXml(xml\XMLDomElement $el){

	}
	/**
	 * Ritorna una stringa del DOM presente in $xml
	 * @param $xml
	 */
	protected function serializeXml( $destinationClass, xml\XMLDom $xml, xml\XMLDom $originalXML , TemplateRef $parentTemplate = null) {
		
		$this->cleanXml($xml->documentElement);


		foreach ( $xml->query ( "//processing-instruction()" ) as $node ) {
			if ($node->parentNode && $node->parentNode->namespaceURI != self::NS) {
				$new = $xml->createTextNode ( "\n" );
				if ($node->nextSibling) {
					$node->parentNode->insertBefore ( $new, $node->nextSibling );
				} else {
					$node->parentNode->appendChild ( $new );
				}
			}
		}

		$cnt = array ();

		$cnt [] = "<?php\n";
		$cnt [] = "// ATal generated template. Do not edit.\n";
		$cnt [] = "// Compiled form : " . $this->getTemplate ()->getRef()->getRealPath(). "\n";
		if($parentTemplate){
			$cnt [] = "// Parent : " . $parentTemplate->getRealPath(). "\n";
		}

		$initNodes = $xml->query ( "//t:init-function", array ("t" => self::NS ) );

		$init = array ();
		$init [] = "\tfunction init(){\n";
		$init [] = "\t\tparent::init();\n";
		$ndRemove = array ();
		$ndRemove2 = array ();
		foreach ( $initNodes as $node ) {
			if (! isset ( $ndRemove [$node->getAttribute ( 'key' )] )) {
				$init [] = '$this->pluginVars[\'' . $node->getAttribute ( 'key' ) . '\'] = call_user_func(function(' . $node->getAttribute ( 'params' ) . '){';
				$init [] = $node->saveXML ( false );
				$init [] = '}, $this->pluginVars[\'' . $node->getAttribute ( 'key' ) . '\']);' . "\n";
			}
			$ndRemove [$node->getAttribute ( 'key' )] = true;
			$ndRemove2 [] = $node;
		}
		$init [] = "\t}\n";

		foreach ( $ndRemove2 as $node ) {
			$node->remove ();
		}

		if ($parentTemplate) {
			$parentCacheName = addcslashes ( $this->tal->getCachePath( $parentTemplate ), "\\" );
			$parentClassName = addcslashes ( $this->tal->getClassName ( $parentTemplate ), "\\" );
			$parentBaseName = addcslashes ( $parentTemplate->getBaseName (), "\\" );

			$cnt [] = "\$this->compile(\$this->convertTemplateName('" . $parentBaseName . "', \$__tal_template_info['templateRef']));\n";
			$cnt [] = "class $destinationClass extends $parentClassName {\n";
		} else {
			$cnt [] = "class $destinationClass extends \\goetas\\atal\\CompiledTemplate{\n";

			$cnt [] = "function display(){\n";
			$cnt [] = "extract(\$this->getData()); \$__tal = \$this->getTal(); ?>\n";

			if ($this->tal->xmlDeclaration) {
				$cnt [] = '<?xml version="1.0" encoding="utf-8"?>' . "\n";
			}
			if ($originalXML->doctype) {
				$cnt [] = $originalXML->saveXML ( $originalXML->doctype ) . "\n";
			}
			// mettendo queste 2 query xpath insieme il php genera i nodi in ordine sbagliato
			foreach ( $xml->query ( "/processing-instruction()" ) as $node ) {
				$cnt [] = $xml->saveXML ( $node );
			}
			foreach ( $xml->query ( "/node()|/text()", array ("t" => self::NS ) ) as $node ) {
				if ($node->namespaceURI != self::NS) {
					$cnt [] = $xml->saveXML ( $node );
				}
			}
			// fine bug

			$cnt [] = "<?php\t}\n";
		}

		if ($ndRemove) {
			$cnt [] = implode ( "", $init );
		}
		foreach ( $xml->query ( "/t:atal-block", array ("t" => self::NS ) ) as $node ) {
			$tcnt = '';
			foreach ( $node->childNodes as $cn ) {
				$tcnt .= $xml->saveXML ( $cn );
			}
			if (substr ( $tcnt, 0, 5 ) == '<?php' && substr ( $tcnt, - 2 ) == '?>') {
				$cnt [] = substr ( $tcnt, 5, - 2 );
			} else {
				throw new \Exception ( "errore atal block" );
			}

		}
		$cnt [] = "}"; // fine classe
		$cnt = implode ( "", $cnt );

		return $cnt;

	}
	/**
	 * Compila un template e salvalo in $destination
	 */
	public function compile($destinationFile, $destinationClass) {

		$xml = $this->toDom ( );
		$originalXML = $xml;

		$xml = $this->getPreXmlFilters ()->applyFilters ( $xml );
		
		$parentTemplatePath = $this->getExtensionTemplate ( $xml );

		if ($parentTemplatePath) {

			$parentTemplateRef = $this->tal->convertTemplateName ( $parentTemplatePath , $this->getTemplate()->getRef());
			// ask to current finder!
			$parentTemplate = $this->getTemplate()->getFinder ()->getTemplate($parentTemplateRef)->getRef();

			$this->findDefBlocks ( $xml->documentElement );
		} else {
			$parentTemplate = null;
		}

		$this->findBlocks ( $xml->documentElement );

		$this->applyTemplates ( $xml->documentElement );
		$this->getPostApplyTemplatesFilters()->applyFilters($xml);
		$xml = $this->getPostXmlFilters ()->applyFilters ( $xml );

		$cnt = $this->serializeXml ( $destinationClass, $xml, $originalXML, $parentTemplate );

		$cnt = $this->getPostFilters ()->applyFilters ( $cnt );

		$cacheName = $destinationFile .".". md5(microtime()) .".tmp";

		if (file_put_contents ($cacheName , $cnt )) {
			rename ( $cacheName , $destinationFile );
		} else {
			throw new Exception ( "Non riesco a salvare il file in cache" );
		}
	}

	public function findDefBlocks(xml\XMLDomElement $node) {

		$res = $node->query ( "/*[@t:extends]/*", array ("t" => self::NS ) );

		$parents = array ();
		$nomi = array ();
		foreach ( $res as $blocco ) {

			$attBlock = $blocco->getAttributeNodeNs ( self::NS, "block" );
			$blockName = $attBlock->value;
			if (! $blockName) {
				throw new Exception ( "Tutti gli elementi di figli di @extends devono essere definizioni di blocco" );
			}
			if (isset ( $nomi [$blockName] )) {
				throw new Exception ( "Dichiarazione duplicata per il blocco '$blockName'" );
			}
			$nomi [$blockName] = true;

			$nodesAttr = array ();
			while ( $blocco->attributes->length ) {
				$nodesAttr [] = $attr = $blocco->attributes->item ( 0 );
				$blocco->removeAttributeNode ( $attr );
			}
			$blocco->setAttributeNs ( self::NS, "block-redef", $blockName );

			while ( count ( $nodesAttr ) ) {
				$blocco->setAttributeNode ( array_shift ( $nodesAttr ) );
			}
			$blocco->removeAttributeNs ( self::NS, "block" );
		}

	}
	public function findBlocks(xml\XMLDomElement $node) {
		$res = $node->query ( "//*[@t:block]", array ("t" => self::NS ) );
		$nomi = array ();
		foreach ( $res as $blocco ) {
			$blockName = $blocco->getAttributeNs ( self::NS, "block" );
			if (isset ( $nomi [$blockName] )) {
				throw new Exception ( "Dichiarazione duplicata per il blocco '$blockName'" );
			}
			$nomi [$blockName] = true;


			$nodesAttr = array ();
			while ( $blocco->attributes->length ) {
				$nodesAttr [] = $attr = $blocco->attributes->item ( 0 );
				$blocco->removeAttributeNode ( $attr );
			}
			while ( count ( $nodesAttr ) ) {
				$nodeAttr = array_shift ( $nodesAttr );
				if($nodeAttr->name=='block' && $nodeAttr->namespaceURI==self::NS){
					$blocco->setAttributeNs ( self::NS, "block-def", $blockName );
					$blocco->setAttributeNs ( self::NS, "block-call", $blockName );
				}else{
					$blocco->setAttributeNode ($nodeAttr);
				}
			}
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
			if ($attr->namespaceURI == self::NS && ! in_array ( $attr->localName, $skip ) && $attr->ownerElement === $node) { // è un attributo tal

				$attPlugin = $this->attributes->attribute ( $attr->localName );
				$attPlugin->setDom ( $node->ownerDocument );

				$attPluginsUsed [] = array ($attPlugin, $node, $attr );
				$continueRule = $attPlugin->start ( $node, $attr );
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

			} elseif ($attr->namespaceURI != self::NS) { // non è un attributo tal{
				$this->applyAttributeVars ( $attr );
			}
		}
		if (! $stopNode) {
			$this->applyTemplatesToChilds ( $childNodes );
			foreach ( $attPluginsUsed as $data ) {
				if ($data [1]->ownerDocument != null) { // nodo ancora non rimosso
					$data [0]->end ( $data [1], $data [2] );
				}
			}
		}
	}
	protected function applyTemplatesToChilds($childNodes) {
		foreach ( $childNodes as $child ) {
			if ($child instanceof xml\XMLDomElement) {
				$this->applyTemplates ( $child );
			} elseif ($child instanceof DOMText) {
				$this->applyTextVars ( $child ); // applica le variabili sul testo
			} elseif ($child instanceof DOMProcessingInstruction) {
				$this->applyTextVars ( $child ); // applica le variabili sul testo
			}
		}
	}
	public static function _replaceTextVars($string) {
		return str_replace ( array ("<![CDATA[{{__NOCDATA__", "__NOCDATA__}}]]>" ), "", $string );
	}
	/**
	 * Applica {@method parsedExpression} ad un nodo DOM di tipo testo (e cdata)
	 * @param $attr
	 * @return void
	 */
	public function applyTextVars(DOMNode $nodo) {
		$mch = array ();
		if (preg_match_all ( $this->currRegex, $nodo->data, $mch )) {
			$xml = $nodo->data;
			foreach ( $mch [0] as $k => $pattern ) {
				$xml = str_replace ( $pattern, '<?php print( ' . $this->parsedExpression ( $mch [1] [$k] ) . "); ?>\n", $xml );
			}
			
			if ($nodo instanceof \DOMText){
				if (! ($nodo instanceof DOMCdataSection)) {
					$xml = "{{__NOCDATA__{$xml}__NOCDATA__}}";
				}
				$newEl = $nodo->ownerDocument->createCDATASection ( $xml );
				if ($nodo->parentNode instanceof DOMNode) {
					$nodo->parentNode->replaceChild ( $newEl, $nodo );
				} else {
					throw new Exception ( $nodo->nodeName . ' non ha un padre. ' );
				}
			}else{
				$nodo->data = $xml;
			}
		}
	}
	public function applyAttributeVars(\DOMAttr $attr) {
		$mch = array ();
		if (preg_match_all ( $this->currRegex, $attr->value, $mch )) {
			$code = '';
			$nodo = $attr->ownerElement;
			if (! $nodo->ownerDocument) {
				//echo htmlentities ( $attr->value );
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
		} else { //  usa il pre modificatre di default
			$parts [] = '';
		}		
		foreach ( $parts as $part ) {
			if (preg_match ( '#(^[a-z][a-z0-9_\\-]*\s*:)#i', $part, $mch )) { // modificatore con parametri
				// modifier con parametri
				$modifierParts = $this->splitExpression ( $part, ':' );
				$modName = array_shift ( $modifierParts );

				$modParams = array ();
				foreach ( $modifierParts as $modifierParam ) {
					$mch = array ();
					if (preg_match ( "/^([a-z][a-z0-9_\\-]*)\\s*\\=(.*)/i", $modifierParam, $mch )) { // parametri con nome
						$exs = trim ( $mch [2] );
						$exs = $exs [0] == "(" && $exs [strlen ( $exs ) - 1] == ")" ? substr ( $exs, 1, - 1 ) : $exs;
						$modParams [$mch [1]] = $this->parsedExpression ( $exs, true );
					} else { // parametri numerici
						$paramStr = trim ( $modifierParam );
						$paramStr = $paramStr [0] == "(" && $paramStr [strlen ( $paramStr ) - 1] == ")" ? substr ( $paramStr, 1, - 1 ) : $paramStr;
						$modParams [] = $this->parsedExpression ( $paramStr, true );
					}
				}
				$var = "\$this->modifiers->modifier('$modName')->modify($var, " . $this->dumpKeyed ( $modParams ) . " )";
			} elseif ($part === '' || preg_match ( '#(^[a-z][a-z0-9_\\-]*$)#i', $part )) { // modificatore senza parametri o di default
				if($part!=='raw'){
					$var = "\$this->modifiers->modifier('$part')->modify($var , array() )";
				}
			} else {
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
	public function splitExpression($str, $splitrer) {
		return static::staticSplitExpression ( $str, $splitrer );
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
			throw new Exception ( "Perentesi non bilanciate nell'espressione '" . implode ( "", $str ) . "'" );
		} elseif ($inApex !== false) {
			throw new Exception ( "Apici non bilanciati nell'espressione '" . implode ( "", $str ) . "'" );
		}
		return $parts;
	}
	public function dumpKeyed(array $parts) {
		$r = ' array(';
		foreach ( $parts as $key => $val ) {
			$r .= "'$key'=>" . $val . ", ";
		}
		return $r . " ) ";
	}
}
