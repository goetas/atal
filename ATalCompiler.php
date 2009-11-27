<?php
class ATalCompiler {
	const NS = ATal::NS;
	protected static $htmlEntities = array('&uArr;' => '&#8657;', '&icirc;' => '&#238;', '&cong;' => '&#8773;', '&clubs;' => '&#9827;', '&thorn;' => '&#254;', '&rarr;' => '&#8594;', '&prime;' => '&#8242;', '&circ;' => '&#710;', '&Kappa;' => '&#922;', '&perp;' => '&#8869;', '&Uuml;' => '&#220;', '&pound;' => '&#163;', '&middot;' => '&#183;', '&larr;' => '&#8592;', '&acute;' => '&#180;', '&quot;' => '&#34;', '&otimes;' => '&#8855;', '&Mu;' => '&#924;', '&ordm;' => '&#186;', '&Theta;' => '&#920;', '&atilde;' => '&#227;', '&notin;' => '&#8713;', '&Xi;' => '&#926;', '&lfloor;' => '&#8970;', '&nbsp;' => '&#160;', '&ni;' => '&#8715;', '&Nu;' => '&#925;', '&Oslash;' => '&#216;', '&ndash;' => '&#8211;', '&ordf;' => '&#170;', '&thetasym;' => '&#977;', '&ne;' => '&#8800;', '&Rho;' => '&#929;', '&real;' => '&#8476;', '&oplus;' => '&#8853;', '&zwnj;' => '&#8204;', '&hearts;' => '&#9829;', '&alpha;' => '&#945;', '&euml;' => '&#235;', '&bdquo;' => '&#8222;', '&cap;' => '&#8745;', '&uacute;' => '&#250;', '&dagger;' => '&#8224;', '&lrm;' => '&#8206;', '&reg;' => '&#174;', '&Sigma;' => '&#931;', '&Tau;' => '&#932;', '&ensp;' => '&#8194;', '&sbquo;' => '&#8218;', '&Dagger;' => '&#8225;', '&sigma;' => '&#963;', '&plusmn;' => '&#177;', '&iota;' => '&#953;', '&emsp;' => '&#8195;', '&radic;' => '&#8730;', '&mdash;' => '&#8212;', '&Uacute;' => '&#218;', '&ecirc;' => '&#234;', '&ntilde;' => '&#241;', '&image;' => '&#8465;', '&uarr;' => '&#8593;', '&sim;' => '&#8764;', '&acirc;' => '&#226;', '&Beta;' => '&#914;', '&Yuml;' => '&#376;', '&shy;' => '&#173;', '&Acirc;' => '&#194;', '&Ucirc;' => '&#219;', '&Eta;' => '&#919;', '&Iacute;' => '&#205;', '&yacute;' => '&#253;', '&apos;' => '&#39;', '&Eacute;' => '&#201;', '&nsub;' => '&#8836;', '&Aring;' => '&#197;', '&Igrave;' => '&#204;', '&ccedil;' => '&#231;', '&empty;' => '&#8709;', '&aelig;' => '&#230;', '&iuml;' => '&#239;', '&infin;' => '&#8734;', '&le;' => '&#8804;', '&kappa;' => '&#954;', '&OElig;' => '&#338;', '&Omega;' => '&#937;', '&prop;' => '&#8733;', '&lArr;' => '&#8656;', '&oslash;' => '&#248;', '&Agrave;' => '&#192;', '&Psi;' => '&#936;', '&auml;' => '&#228;', '&sdot;' => '&#8901;', '&eacute;' => '&#233;', '&beta;' => '&#946;', '&exist;' => '&#8707;', '&Iuml;' => '&#207;', '&Scaron;' => '&#352;', '&Egrave;' => '&#200;', '&Zeta;' => '&#918;', '&Omicron;' => '&#927;', '&oline;' => '&#8254;', '&int;' => '&#8747;', '&spades;' => '&#9824;', '&fnof;' => '&#402;', '&prod;' => '&#8719;', '&yuml;' => '&#255;', '&rsquo;' => '&#8217;', '&weierp;' => '&#8472;', '&Ograve;' => '&#210;', '&Pi;' => '&#928;', '&there4;' => '&#8756;', '&otilde;' => '&#245;', '&szlig;' => '&#223;', '&darr;' => '&#8595;', '&aacute;' => '&#225;', '&THORN;' => '&#222;', '&ucirc;' => '&#251;', '&iexcl;' => '&#161;', '&ograve;' => '&#242;', '&rArr;' => '&#8658;', '&asymp;' => '&#8776;', '&aring;' => '&#229;', '&rho;' => '&#961;', '&sect;' => '&#167;', '&pi;' => '&#960;', '&macr;' => '&#175;', '&scaron;' => '&#353;', '&copy;' => '&#169;', '&sube;' => '&#8838;', '&diams;' => '&#9830;', '&brvbar;' => '&#166;', '&uuml;' => '&#252;', '&sup3;' => '&#179;', '&minus;' => '&#8722;', '&Otilde;' => '&#213;', '&lang;' => '&#9001;', '&ldquo;' => '&#8220;', '&omega;' => '&#969;', '&ang;' => '&#8736;', '&lowast;' => '&#8727;', '&sup1;' => '&#185;', '&sup2;' => '&#178;', '&Ccedil;' => '&#199;', '&rdquo;' => '&#8221;', '&nabla;' => '&#8711;', '&Chi;' => '&#935;', '&lceil;' => '&#8968;', '&Ntilde;' => '&#209;', '&and;' => '&#8743;', '&mu;' => '&#956;', '&delta;' => '&#948;', '&bull;' => '&#8226;', '&raquo;' => '&#187;', '&part;' => '&#8706;', '&iquest;' => '&#191;', '&psi;' => '&#968;', '&Icirc;' => '&#206;', '&zwj;' => '&#8205;', '&euro;' => '&#8364;', '&Phi;' => '&#934;', '&oelig;' => '&#339;', '&para;' => '&#182;', '&ocirc;' => '&#244;', '&ouml;' => '&#246;', '&omicron;' => '&#959;', '&Ugrave;' => '&#217;', '&Gamma;' => '&#915;', '&sum;' => '&#8721;', '&isin;' => '&#8712;', '&sup;' => '&#8835;', '&AElig;' => '&#198;', '&dArr;' => '&#8659;', '&micro;' => '&#181;', '&agrave;' => '&#224;', '&Delta;' => '&#916;', '&Ecirc;' => '&#202;', '&gamma;' => '&#947;', '&tau;' => '&#964;', '&crarr;' => '&#8629;', '&or;' => '&#8744;', '&epsilon;' => '&#949;', '&rsaquo;' => '&#8250;', '&frac34;' => '&#190;', '&Euml;' => '&#203;', '&rang;' => '&#9002;', '&times;' => '&#215;', '&thinsp;' => '&#8201;', '&tilde;' => '&#732;', '&piv;' => '&#982;', '&Prime;' => '&#8243;', '&curren;' => '&#164;', '&laquo;' => '&#171;', '&cedil;' => '&#184;', '&oacute;' => '&#243;', '&permil;' => '&#8240;', '&Iota;' => '&#921;', '&loz;' => '&#9674;', '&ETH;' => '&#208;', '&trade;' => '&#8482;', '&xi;' => '&#958;', '&cent;' => '&#162;', '&Aacute;' => '&#193;', '&cup;' => '&#8746;', '&yen;' => '&#165;', '&chi;' => '&#967;', '&upsih;' => '&#978;', '&lambda;' => '&#955;', '&Alpha;' => '&#913;', '&sub;' => '&#8834;', '&igrave;' => '&#236;', '&gt;' => '&#62;', '&harr;' => '&#8596;', '&phi;' => '&#966;', '&deg;' => '&#176;', '&not;' => '&#172;', '&Atilde;' => '&#195;', '&lsaquo;' => '&#8249;', '&Oacute;' => '&#211;', '&divide;' => '&#247;', '&Yacute;' => '&#221;', '&nu;' => '&#957;', '&uml;' => '&#168;', '&rceil;' => '&#8969;', '&frac12;' => '&#189;', '&theta;' => '&#952;', '&upsilon;' => '&#965;', '&Auml;' => '&#196;', '&Ocirc;' => '&#212;', '&iacute;' => '&#237;', '&hArr;' => '&#8660;', '&equiv;' => '&#8801;', '&eta;' => '&#951;', '&Upsilon;' => '&#933;', '&frac14;' => '&#188;', '&egrave;' => '&#232;', '&supe;' => '&#8839;', '&ge;' => '&#8805;', '&rfloor;' => '&#8971;', '&Epsilon;' => '&#917;', '&ugrave;' => '&#249;', '&Lambda;' => '&#923;', '&eth;' => '&#240;', '&zeta;' => '&#950;', '&frasl;' => '&#8260;', '&Ouml;' => '&#214;', '&forall;' => '&#8704;', '&rlm;' => '&#8207;', '&hellip;' => '&#8230;', '&sigmaf;' => '&#962;', '&alefsym;' => '&#8501;', '&lsquo;' => '&#8216;' );
	/**
	 * @var ATalPluginLoader
	 */
	protected $attrs;
	/**
	 * @var ATalModifier
	 */
	protected $modifierManager;
	/**
	 * @var ATalAttrRuntime
	 */
	protected $runtimeAttrManager;
	/**
	 * @var string
	 */
	protected $defaultModifier = 'escape';
	/**
	 * @var array
	 */
	protected $filters = array();
	/**
	 * @var array
	 */
	protected $selectors = array();
	/**
	 * @var array
	 */
	protected $prefilters = array();
	/**
	 * @var ATal
	 */
	protected $tal;
	/**
	 * @var string
	 */
	protected $template;
	function __construct(ATal $tal) {
		$this->tal = $tal;
		$this->attrs = new ATalPluginLoader( dirname( __FILE__ ) . DIRECTORY_SEPARATOR . 'plugins' . DIRECTORY_SEPARATOR . 'attrs' . DIRECTORY_SEPARATOR . 'compilable' );
		$this->modifierManager = $this->tal->getModiferManager();
		$this->runtimeAttrManager = $this->tal->getRuntimeAttrManager();
	}
	function __clone() {
		$this->attrs = new ATalPluginLoader( dirname( __FILE__ ) . DIRECTORY_SEPARATOR . 'plugins' . DIRECTORY_SEPARATOR . 'attrs' . DIRECTORY_SEPARATOR . 'compilable' );
	}
	function addCompiledAttr($name, ATalDynamicClass $plugin) {
		$this->attrs->add( $name, $plugin );
	}
	public function getTemplate() {
		return $this->template;
	}
	/**
	 *
	 * @param ATal_XMLDom $tipo
	 * @param $dom
	 * @return ATalSelector
	 */
	public function getSelector($tipo, ATal_XMLDom $dom){
		$ref = new ReflectionClass($this->selectors[$tipo]);
		if ($ref->isSubclassOf('ATalSelector')){
			return $ref->newInstance($dom,$this->tal);
		}else{
			throw new ATalException("Non trovo un selettore adatto per '$tipo'");
		}
	}
	public function addSelector($name, $class){
		$this->selectors[$name]=$class;
	}
	public function getSelectors() {
		return $this->selectors;
	}

	public function compile($tpl, $tipo, $query) {
		$this->template = $tpl;
		$fileName = $this->tal->getCompileDir() . DIRECTORY_SEPARATOR . basename( $tpl ) . "_" . md5( $tipo . $query . realpath( $tpl ) ) . ".php";

		if($this->tal->debug || ! is_file( $fileName ) || filemtime( $fileName ) < filemtime( $tpl )){
			$this->attrs->reset();
			$xml = new ATal_XMLDom( );
			$root = $xml->addChildNS( self::NS, "atal-content" );

			$xmlString = file_get_contents( $tpl );

			$xmlString = str_replace( array_keys( self::$htmlEntities ), self::$htmlEntities, $xmlString );

			$tplDom = ATal_XMLDom::loadXMLString( $xmlString );
			try{
				if($tipo){
					$selector = $this->getSelector($tipo,$tplDom);
					$res = $selector->select($query);
					foreach ( $res as $node ){
						$root->appendChild( $xml->importNode( $node, 1 ) );
					}
				}else{
					$root->appendChild( $xml->importNode( $tplDom->documentElement, 1 ) );
				}
			}catch(\Exception $e){
				die($e);
			}
			foreach ( $xml->query( "//t:t[not(@t:omit)]", array("t" => self::NS ) ) as $node ){
				$node->setAttributeNS( ATal::NS, "omit", 'true' );
			}
			foreach ( $xml->query( "//*" ) as $node ){
				$node->setAttributeNS( ATal::NS, "id", uniqid() );
			}
			$this->applyTemplats( $xml->documentElement );

			$this->addPreWriteFilter( array($this, 'removeTIDAttrs' ) );
			$this->applyPreFilters( $xml );

			if(ini_get( "short_open_tag" )){
				$this->addPostFilter( array(__CLASS__, 'replaceShortTags' ) );
			}
			$this->addPostFilter( array(__CLASS__, 'removeXMLNS' ) );

			$cnt = '';
			if($this->tal->xmlDeclaration){
				$cnt .= '<?xml version="1.0" encoding="utf-8"?>' . "\n";
			}
			if($tplDom->doctype && $this->tal->dtdDeclaration){
				$cnt .= $tplDom->saveXML( $tplDom->doctype ) . "\n";
			}

			// mettendo queste 2 query xpath insieme il php genera i nodi in ordine sbagliato
			foreach ( $xml->query( "/processing-instruction()" ) as $node ){
				$cnt .= $xml->saveXML( $node );
			}
			foreach ( $xml->query( "/t:atal-content/node()|/text()", array("t" => self::NS ) ) as $node ){
				$cnt .= $xml->saveXML( $node );
			}
			$cnt = $this->applyFilters( $cnt );
			// fine bug


			file_put_contents( $fileName, $cnt );
			chmod( $fileName, 0666 );
		}
		return $fileName;
	}
	public function removeTIDAttrs(ATal_XMLDom  $xml) {
		foreach ( $xml->query( "//*[@t:id]/@t:id", array("t" => ATal::NS ) ) as $tt ){
			$tt->ownerElement->removeAttributeNode( $tt );
		}
	}
	public static function replaceShortTags($str) {
		return str_replace( "<?xml ", "<?php print( \"<?xml \" ) ?>", $str );
	}
	public static function removeXMLNS($str) {
		$str = preg_replace( "/xmlns:([a-zA-Z][a-zA-Z0-9_\-]*)=" . preg_quote( '"' . self::NS . '"', "/" ) . "/", "", $str );
		return str_replace( "xmlns=\"" . self::NS . "\"", "", $str );
	}

	public function applyTemplats(ATal_XMLDomElement $node, $skip = array()) {
		$attributes = array();
		$childNodes = array();

		foreach ( $node->attributes as $attr ){
			$attributes [] = $attr;
		}
		foreach ( $node->childNodes as $child ){
			$childNodes [] = $child;
		}

		foreach ( $attributes as $attr ){
			if($attr->namespaceURI == self::NS && $attr->localName == 'id'){

			}elseif($attr->namespaceURI == self::NS && isset( $this->attrs [$attr->localName] )){

				$attPlugin = $this->attrs->newInstance( $attr->localName, $this, $node->ownerDocument );
				if(! in_array( $attr->localName, $skip ) && $attr->ownerElement === $node && $attPlugin->start( $node, $attr->value ) === false){
					try{
						$node->removeAttributeNode( $attr );
					}catch ( DOMException $e ){

					}
					break;
				}else{
					try{
						$node->removeAttributeNode( $attr );
					}catch ( DOMException $e ){

					}
				}
			}elseif($attr->namespaceURI == self::NS){
				$this->runtimeAttrManager->setName( $attr->name );

				$content = '';
				foreach ( $node->childNodes as $child ){
					$content .= $node->ownerDocument->saveXML( $child );
				}
				$params = array();
				foreach ( self::splitExpression( $attr->value, ";" ) as $part ){
					list ( $k, $v ) = self::splitExpression( $part, "=" );
					if(strlen( $v )){
						$params [$k] = $v;
					}else{
						$params [] = $k;
					}
				}
				$pi = $node->ownerDocument->createProcessingInstruction( "php", "print ( " . $this->runtimeAttrManager->runAttr( $params, $content ) . " ); " );

				$node->removeChilds();
				$node->appendChild( $pi );
				$node->removeAttributeNode( $attr );
			}else{
				$this->applyAttributeVars( $attr );
			}
		}

		foreach ( $childNodes as $child ){
			if($child instanceof ATal_XMLDomElement){
				$this->applyTemplats( $child );
			}elseif($child instanceof DOMText || $child instanceof DOMCDATASection){
				$this->applyTextVars( $child );
			}
		}
	}
	/**
	 * Espressione regolare per trovare le variabili racchiuse tra parentresi graffe
	 * @var string
	 */
	const VAR_REGEX = '/\\{([\'a-z\$\\\\][^\\}]*)}/';
	public function applyTextVars($nodo) {
		$mch = array();
		if($nodo instanceof DOMText && preg_match_all(self::VAR_REGEX, $nodo->data, $mch )){
			$cdata = ($nodo instanceof DOMCdataSection);

			if($cdata){
				$xml = '<![CDATA[' . $nodo->data . ']]>';

			}else{
				$xml = $nodo->data;
			}

			$tdom = new ATal_XMLDom( );
			$frag = $tdom->createDocumentFragment();
			foreach ( $mch [0] as $k => $pattern ){
				$xml = str_replace( $pattern, ($cdata ? "]]>" : "") . '<?php echo ' . ($cdata ? "'<![CDATA['." : "") . $this->parsedExpression( $mch [1] [$k] ) . ($cdata ? ".']]>'" : "") . '; ?>' . ($cdata ? "<![CDATA[" : ""), $xml );
			}

			if(! $cdata){
				$xml = str_replace( "&", "&amp;", $xml );
			}
			$frag->appendXML( $xml );

			$tdom->appendChild( $frag );

			foreach ( $tdom->childNodes as $k => $el ){
				$nel [$k] = $nodo->ownerDocument->importNode( $el, true );
				if($nodo->parentNode instanceof DOMNode ){
					$nodo->parentNode->insertBefore( $nel [$k], $nodo );
				}else{
					throw new ATalException($nodo->nodeName.' non ha un padre. '.$nodo->nodeValue);
				}

				if($nel [$k] instanceof ATal_XMLDomElement){
					$this->applyTemplats( $nel [$k] );
				}
			}
			$nodo->parentNode->removeChild( $nodo );
		}
	}
	public function applyAttributeVars($attr) {
		$mch = array();
		if(preg_match_all(self::VAR_REGEX, $attr->value, $mch )){
			$code = '';
			$nodo = $attr->ownerElement;
			$val = $attr->value;

			foreach ( $mch [1] as $k => $mc ){
				$attName = "\$__tal_attr_" . md5( $k . microtime() );
				$code .= "$attName  =  " . $this->parsedExpression( $mc ) . " ;\n ";
				$val = str_replace( $mch [0] [$k], "[#tal_attr#" . $attName . "#tal_attr#]", $val );
			}
			$attr->value = htmlspecialchars( $val, ENT_QUOTES, 'UTF-8' );

			$pi = $nodo->ownerDocument->createProcessingInstruction( "php", $code );
			$nodo->parentNode->insertBefore( $pi, $nodo );
		}
	}
	public function addPlugin($name, ATalAttrCompilablePlugin $plugin) {
		$this->attrs [$name] = $plugin;
	}
	public function removePlugin($name) {
		unset( $this->attrs [$name] );
	}
	public function addPostFilter($callback) {
		if(is_callable( $callback )){
			$this->filters [] = $callback;
		}else{
			throw new ATalException( "callback non valida per  addPostFilter '" . (is_array( $callback ) ? implode( ",", $callback ) : $callback) . "'" );
		}
	}
	public function addPreWriteFilter($callback) {
		if(is_callable( $callback )){
			$this->prefilters [] = $callback;
		}else{
			throw new ATalException( "callback non valida per  addPreWriteFilter '" . (is_array( $callback ) ? implode( ",", $callback ) : $callback) . "'" );
		}
	}

	public function setDefaultModifier($modifierName) {
		$this->defaultModifier = $modifierName;
	}
	protected function applyPreFilters(ATal_XMLDom $dom) {
		foreach ( $this->prefilters as $filter ){
			call_user_func( $filter, $dom );
		}
		return $dom;
	}
	protected function applyFilters($string) {
		foreach ( $this->filters as $filter ){
			$string = call_user_func( $filter, $string );
		}
		return preg_replace( "/" . preg_quote( "[#tal_attr#", "/" ) . "(" . preg_quote( '$', "/" ) . "[a-zA-Z_\x7f-\xff][a-zA-Z0-9_\x7f-\xff]*)" . preg_quote( "#tal_attr#]", "/" ) . "/", "<?php print( \\1 ) ?>", $string );
	}

	/* compiler utils */
	public function parsedExpression($exp, $skip = false) {
		$parts = self::splitExpression( $exp, '|' );
		$var = trim( array_shift( $parts ) );
		$mch = array();

		if($skip){
			//$parts [] = $defaultModifier;
		}elseif(preg_match( "/^([a-zA-Z_]+)\\s*:\\s*([^:]+)/", $var, $mch )){ // cerco un pre-modifier
			$parts [] = trim( $mch [1] );
			$var = trim( $mch [2] );
		}elseif($this->defaultModifier){
			$parts [] = $this->defaultModifier;
		}
		foreach ( $parts as $part ){
			if(preg_match( '#(^[a-z][a-z0-9_\\-]*$)|(^[a-z][a-z0-9_\\-]*\s*:)#i', $part, $mch )){
				$modifierParts = self::splitExpression( $part, ':' );
				$mname = array_shift( $modifierParts );
				$modifierManager = clone $this->modifierManager;
				$modifierManager->setModifierName( $mname );
				foreach ( $modifierParts as $modifierParam ){
					$mch = array();
					if(preg_match( "/^([a-z][a-z0-9_\\-]*)\\s*\\=(.*)/i", $modifierParam, $mch )){
						$modifierManager->addNamedParam( $mch [1], $this->parsedExpression( trim( $mch [2], "()" ), true ) );
					}else{
						$paramStr = trim($modifierParam);
						$paramStr = $paramStr[0]=="(" && $paramStr[strlen($paramStr)-1]==")"?substr($paramStr,1,-1):$paramStr;
						$modifierManager->addParam( $this->parsedExpression( $paramStr , true ) );
					}
				}
				$var = $modifierManager->runModifier( $var );
				unset( $modifierManager );
			}else{
				throw new ATalException( "errore di sintassi vicino a '$part'" );
			}
		}
		return $var;
	}

	public static function splitExpression($str, $splitrer) {
		$str = str_split( $str, 1 );
		$str [] = " ";
		$str_len = count( $str );

		$splitrer = str_split( $splitrer, 1 );
		$splitrer_len = count( $splitrer );

		$parts = array();
		$inApex = false;
		$next = 0;
		$pcount = 0;
		for($i = 0; $i < $str_len; $i ++){
			if(! $inApex && ($i === 0 || $str [$i - 1] !== "\\") && ($str [$i] === "\"" || $str [$i] === "'")){ // ingresso
				$inApex = $str [$i];
			}elseif($inApex === $str [$i] && $str [$i - 1] !== "\\"){ // uscita
				$inApex = false;
			}
			if($inApex === false && $str [$i] === "("){
				$pcount ++;
			}elseif($inApex === false && $str [$i] === ")"){
				$pcount --;
			}
			if($inApex === false && $pcount === 0 && (array_slice( $str, $i, $splitrer_len ) == $splitrer || $i == ($str_len - 1))){
				$val = trim( implode( '', array_slice( $str, $next, $i - $next ) ) );
				if(strlen( $val )){
					$parts [] = $val;
				}
				$next = $i + $splitrer_len;
			}
		}
		return $parts;
	}
	public static function implodeKeyed(array $parts) {
		$r = '';
		foreach ( $parts as $key => $val ){
			$r .= "'$key'=>" . $val . ",\n";
		}
		return $r;
	}
}
