<?php
namespace goetas\atal;
use DOMException;
use InvalidArgumentException;
use ReflectionClass;
class ATal {
	const NS = "ATal";
	/**
	 * directory di file comiplati
	 * @var string
	 */
	protected $compileDir;
	
	/**
	 * @var goetas\atal\loaders\Modifiers
	 */
	protected $modifiers;

	/**
	 * @var BaseRuntimeAttribute
	 */
	protected $baseRuntimeAttribute;
	
	/**
	 * @var goetas\atal\loaders\Services
	 */
	protected $services;
	
		
	public $xmlDeclaration = false;
		
	public $debug = 0;
	
	protected $scope = array ();
	protected $data = array ();
	
	
	public function __construct($compileDir = null, $defaultModifier='raw') {
		
		$this->addScope ( );
		if ($compileDir !== null) {
			$this->setCompileDir ( $compileDir );
		}
		
		$this->modifiers = new loaders\Modifiers ( $this ,$defaultModifier); 
		$this->services = new loaders\Services ( $this );
	
		$this->setup();
	}
	/**
	 * 
	 * @return \goetas\atal\loaders\Modifiers 
	 */
	function getServices(){
		return $this->services;
	}
	protected function setup() {
		$this->modifiers->addDefaultPlugin( array($this,'_defaultModifiers') , __NAMESPACE__.'\IModifier');
	}
	protected $compilerSetups = array();
	public function addCompilerSetup($callback) {
		if(is_callable($callback)){
			$this->compilerSetups[]=$callback;
		}else{
			throw new InvalidArgumentException ( "Callback non valida per " . __METHOD__ );
		}
	}
	
	protected function setupCompiler(Compiler $compiler) {

		$compiler->getPreXmlFilters()->addFilter(array($this,'_addTIDAttrs'));
		$compiler->getPreXmlFilters()->addFilter(array($this,'_handleT'));
		
		$compiler->getPostXmlFilters()->addFilter(array($this,'_removeTIDAttrs'));
		
		$compiler->getPostFilters()->addFilter(array($this,'_removeXmlns'));
	
		$compiler->getPostFilters()->addFilter(array($this,'_replaceShortPI'));
		
		$compiler->getAttributes()->addDefaultPlugin( array($this,'_defaultAttributes'), __NAMESPACE__.'\IAttribute' );
		
		$compiler->getSelectors()->addDefaultPlugin( array($this,'_defaultSelectors') , __NAMESPACE__.'\ISelector');
		
		foreach ($this->compilerSetups as $callback) {
			call_user_func($callback,$compiler,$this);
		}	
	}
	public function _handleT(xml\XMLDom $xml) {
		foreach ( $xml->query ( "//t:t[not(@t:omit)]", array ("t" => self::NS ) ) as $node ) {
			$node->setAttributeNS ( self::NS, "omit", 'true' );
		}
		return $xml;
	}
	public function _addTIDAttrs(xml\XMLDom $xml) {
		foreach ( $xml->query ( "//*" ) as $node ) {
			$node->setAttributeNS ( self::NS, "id", uniqid () );
		}
		return $xml;
	}
	public function _removeTIDAttrs(xml\XMLDom $xml) {
		foreach ( $xml->query ( "//*[@t:id]/@t:id", array ("t" => self::NS ) ) as $tt ) {
			$tt->ownerElement->removeAttributeNode ( $tt );
		}
		return $xml;
	}
	public function _removeXmlns($str) {
		return preg_replace('#<(.*) xmlns:[a-zA-Z0-9]+=("|\')'.self::NS.'("|\')(.*)>#m',"<\\1\\4>",$str);
	}
	public function _replaceShortPI($str) {
		return preg_replace_callback( "#\\<\\?([a-z]+) #", function($mch){
			if($mch[1]=="php"){
				return "<?$mch[1] ";
			}else{
				return "<?php print( \"<?$mch[1] \" ); ?>";
			}
		}, $str );
	}
	/**
	 * 
	 * @return \goetas\atal\loaders\Modifiers 
	 */
	public function getModifiers() {
		return $this->modifiers;
	}
	function __clone() {
		$this->clear ();
	}	
	protected function runCompiled($__file) {
		extract ( $this->getData () );
		$__tal = $this;
		$__tal_modifiers = $this->getModifiers();
		include $__file;
	}
	
	function _defaultAttributes($attrName) {
		$cname = "Attribute_".preg_replace("/[^a-z0-9_]/i","_",$attrName); 
		$fullCname = __NAMESPACE__."\\plugins\\attributes\\$cname";
		if(class_exists($fullCname)){
			return new ReflectionClass($fullCname);
		}
	}
	function _defaultModifiers($attrName) {
		$cname = "Modifier_".preg_replace("/[^a-z0-9_]/i","_",$attrName); 
		$fullCname = __NAMESPACE__."\\plugins\\modifiers\\$cname";
		if(class_exists($fullCname)){
			return new ReflectionClass($fullCname);
		}
		if(is_callable($attrName)){ // funzione standard di php
			return new BasePhpModifier($attrName);
		}
	}	
	function _defaultSelectors($attrName) {
		$cname = "Selector_".preg_replace("/[^a-z0-9_]/i","_",$attrName); 
		$fullCname = __NAMESPACE__."\\plugins\\selectors\\$cname";
		if(class_exists($fullCname)){
			return new ReflectionClass($fullCname);
		}
	}
	public function parseUriParts($templatePath) {
		list ( $tpl, $query ) = explode ( '#', $templatePath, 2 );
		$mch = array ();
		$tipo = null;
		
		if (strlen ( $query ) && preg_match ( "/^([a-z]+)\\s*:(.+)$/i", $query, $mch )) {
			$tipo = $mch [1];
			$query = $mch [2];
		} elseif (strlen ( $query )) {
			$tipo = "id";
		}
		return array (trim ( $tpl ), $tipo, $tipo ? $query : null );
	}
	public function output($templatePath) {
		try {
			list ($tpl, $tipo, $query) = $this->parseUriParts($templatePath); 
		
			$compiledFile = $this->getCacheName($templatePath);
			if(!is_file($tpl)){
				throw new Exception ( "Non trovo il file '$tpl' per poter iniziare la compilazione" );
			}elseif( $this->isChanged($compiledFile, $tpl)) {
				$compiler = new Compiler ( $this );
				$this->setupCompiler($compiler);
				$compiler->compile ( $tpl, $tipo, $query ,$compiledFile);
			}
			$this->runCompiled ( $compiledFile );
		} catch ( DOMException $e ) {
			throw new Exception ( "Errore durante la compilazione del file '$templatePath' (" . $e->getMessage () . ")" );
		}
	}
	protected function isChanged($cacheFile, $originalFile){
		return $this->debug || ! is_file ( $cacheFile ) || filemtime ( $cacheFile ) < filemtime ( $originalFile );
	}
	public function get($tpl) {
		ob_start ();
		$this->output ( $tpl );
		return ob_get_clean ();
	}
	protected function getCacheName($tpl) {
		return $this->getCompileDir () . DIRECTORY_SEPARATOR . basename ( $tpl ) . "_" . md5 ( $tpl.strval ( $this->xmlDeclaration ) . getcwd() ) . ".php";
	}
	public function getCompileDir() {
		if ($this->compileDir === null) {
			throw new Exception ( 'The compile directory must be set' );
		}
		return $this->compileDir;
	}
	public function setCompileDir($dir) {
		
		$this->compileDir = rtrim ( $dir, '/\\' );
		if (is_writable ( $this->compileDir ) === false) {
			throw new Exception ( 'The compile directory must be writable, chmod "' . $this->compileDir . '" to make it writable' );
		}
	}
	/**
	 * 
	 * @param int $olderThan
	 * @return int
	 */
	public function clearCache($olderThan = -1) {
		$cacheDirs = new RecursiveDirectoryIterator ( $this->getCompileDir () );
		$cache = new RecursiveIteratorIterator ( $cacheDirs );
		$expired = time () - $olderThan;
		$count = 0;
		foreach ( $cache as $file ) {
			if ($cache->isDot () || $cache->isDir () || substr ( $file, - 5 ) !== '.html') {
				continue;
			}
			if ($cache->getCTime () < $expired) {
				$count += unlink ( ( string ) $file ) ? 1 : 0;
			}
		}
		return $count;
	}
	/**
	 * @return array
	 */
	public function &getData() {
		return $this->data;
	}
	
	public function addScope(array $vars = array()) {
		unset ( $vars ["this"], $vars ["__file"] );
		$this->scope [] = array_merge ( $this->data, $vars );
		end ( $this->scope );
		$this->data = &$this->scope [key ( $this->scope )];
	}
	public function removeScope() {
		array_pop ( $this->scope );
		end ( $this->scope );
		$this->data = &$this->scope [key ( $this->scope )];
	}
	function assign($varName, $value = null) {
		if ($varName != '') {
			return $this->data [$varName] = $value;
		}
		return null;
	}
	public function __set($varName, $value) {
		$this->data [$varName] = $value;
	}
	public function &__get($varName) {
		return $this->data [$varName];
	}
	
	/**
	 * assigns values to template variables by reference
	 *
	 * @param string $tpl_var the template variable name
	 * @param mixed $value the referenced value to assign
	 */
	function assignByRef($varName, &$value) {
		if ($varName != '') {
			$this->data [$varName] = &$value;
		}
	}
	public function clear() {
		$this->scope = array ();
		$this->data = array ();
		$this->addScope ();
	}
	/**
	 * appends values to template variables
	 *
	 * @param array|string $tpl_var the template variable name(s)
	 * @param mixed $value the value to append
	 */
	function append($varName, $value = null, $merge = false) {
		if ($varName != '' && isset ( $value )) {
			if (! @is_array ( $this->data [$varName] )) {
				settype ( $this->data [$varName], 'array' );
			}
			if ($merge && is_array ( $value )) {
				foreach ( $value as $_mkey => $_mval ) {
					$this->data [$varName] [$_mkey] = $_mval;
				}
			} else {
				$this->data [$varName] [] = $value;
			}
		}
	}
}