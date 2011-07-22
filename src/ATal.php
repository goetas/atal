<?php
namespace goetas\atal;
use DOMException;
use InvalidArgumentException;
use ReflectionClass;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
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
	 * @var \goetas\atal\BaseRuntimeAttribute
	 */
	protected $baseRuntimeAttribute;
	
	/**
	 * @var goetas\atal\loaders\Services
	 */
	protected $services;
	/**
	 * insieme di callbackper configurare a runtime il compilatore
	 * @var array
	 */
	protected $compilerSetups = array();	
	/**
	 * Includi la dichiarazione XML nel file di output
	 * @var bool
	 */	
	public $xmlDeclaration = false;
	/**
	 * Attiva la modalità di debug (ricompila sempre i template)
	 * @var bool
	 */
	public $debug = 0;
	
	protected $scope = array ();
	protected $data = array ();
	
	protected $loadMap = array();
	
	/**
	 * 
	 * @param string $compileDir cartella da usare per la cache dei templates compilati
	 * @param string $defaultModifier pre-modificatore di default. "escape" è il modificatore di default
	 */
	public function __construct($compileDir = null, $defaultModifier='escape') {
		
		$this->addScope ( );
		if ($compileDir !== null) {
			$this->setCompileDir ( $compileDir );
		}
		
		$this->modifiers = new loaders\Modifiers ( $this , $defaultModifier); 
		$this->services = new loaders\Services ( $this );
	
		$this->setup();
		
		spl_autoload_register (array($this, 'templateLoader'));
	}
	public function templateLoader($class){
		if(preg_match("/^ATal_[a-f0-9]{32}/", $class)){
			require $this->getCompileDir()."/". $class.".php";
		}
	}
	/**
	 * Ritorna il gestore dei servizi
	 * @return \goetas\atal\loaders\Services 
	 */
	function getServices(){
		return $this->services;
	}
	/**
	 * Configurazione di atal. Sovrascrivere la funzione per aggiungere funzionalità ad atal. 
	 * Chiamata nel costruttore
	 */
	protected function setup() {
		$this->modifiers->addDefaultPlugin( array($this,'_defaultModifiers') , __NAMESPACE__.'\IModifier');
	}
	public function addCompilerSetup($callback) {
		if(is_callable($callback)){
			$this->compilerSetups[]=$callback;
		}else{
			throw new InvalidArgumentException ( "Callback non valida per " . __METHOD__ );
		}
	}
	/**
	 * Metodo che serve a configurare il compilatore.
	 * Utile in fase di estensione di ATal, per aggiungere nuove funzionalità.
	 * @param \goetas\atal\Compiler $compiler
	 */
	protected function setupCompiler(Compiler $compiler) {

		$compiler->getPreXmlFilters()->addFilter(array($this,'_addTIDAttrs'));
		$compiler->getPreXmlFilters()->addFilter(array($this,'_handleT'));
		
		$compiler->getPostXmlFilters()->addFilter(array($this,'_removeTIDAttrs'));
		
		$compiler->getPostFilters()->addFilter(array($this,'_removeXmlns'));
	
		$compiler->getPostFilters()->addFilter(array($this,'_replaceShortPI'));
		
		$compiler->getAttributes()->addDefaultPlugin( array($this,'_defaultAttributes'), __NAMESPACE__.'\IAttribute' );
		
		$compiler->getSelectors()->addDefaultPlugin( array($this,'_defaultSelectors') , __NAMESPACE__.'\ISelector');
		
		foreach ($this->compilerSetups as $callback) {
			call_user_func($callback, $compiler, $this);
		}	
	}
	/**
	 * funzione che gestisce il tag "<t:t></t:t>" e gli aggiune l'attributo t:omit="true"
	 * @param xml\XMLDom $xml
	 * @return xml\XMLDom
	 */
	public function _handleT(xml\XMLDom $xml) {
		foreach ( $xml->query ( "//t:t[not(@t:omit)]", array ("t" => self::NS ) ) as $node ) {
			$node->setAttributeNS ( self::NS, "omit", 'true' );
		}
		return $xml;
	}
	/**
	 * Aggiunge a tutti i tag l'attributo ID unico. utile in fase di duplicazione dei nodi.
	 * @param xml\XMLDom $xml
	 * @return xml\XMLDom
	 */	
	public function _addTIDAttrs(xml\XMLDom $xml) {
		foreach ( $xml->query ( "//*" ) as $node ) {
			$node->setAttributeNS ( self::NS, "id", uniqid () );
		}
		return $xml;
	}
	/**
	 * Rimuove tutti i tag t:id aggiunti dalla fuunzione {@method _addTIDAttrs()}
	 * @param xml\XMLDom $xml
	 * @return xml\XMLDom
	 */
	public function _removeTIDAttrs(xml\XMLDom $xml) {
		foreach ( $xml->query ( "//*[@t:id]/@t:id", array ("t" => self::NS ) ) as $tt ) {
			$tt->ownerElement->removeAttributeNode ( $tt );
		}
		return $xml;
	}
	/**
	 * Rimuove gli xmlns di ATal
	 * @param string $str
	 * @return string
	 */
	public function _removeXmlns($str) {
		return preg_replace('#<(.*) xmlns:[a-zA-Z0-9]+=("|\')'.self::NS.'("|\')(.*)>#m',"<\\1\\4>", $str);
	}
	/**
	 * Sistema eventuali tag &lt;?php in semplice testo. 
	 * Questo impedisce l'inserimento di php processing instruction all'interno dei templates- 
	 * @param string $str
	 * @return string
	 */
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
	 * Ritorna il gestore dei modificatori. 
	 * @return \goetas\atal\loaders\Modifiers 
	 */
	public function getModifiers() {
		return $this->modifiers;
	}
	function __clone() {
		$this->clear ();
	}	
	/**
	 * Restituisce la ReflectionClass relativa al plugin "attributo" $attrName 
	 * @param string $attrName
	 * @return ReflectionClass
	 */
	function _defaultAttributes($attrName) {
		$cname = "Attribute_".preg_replace("/[^a-z0-9_]/i","_", $attrName); 
		$fullCname = __NAMESPACE__."\\plugins\\attributes\\$cname";
		if(class_exists($fullCname)){
			return new ReflectionClass($fullCname);
		}
	}
	/**
	 * Restituisce la ReflectionClass relativa al plugin "modificatore" $attrName 
	 * @param string $attrName
	 * @return ReflectionClass
	 */
	function _defaultModifiers($attrName) {
		$cname = "Modifier_".preg_replace("/[^a-z0-9_]/i","_", $attrName); 
		$fullCname = __NAMESPACE__."\\plugins\\modifiers\\$cname";
		if(class_exists($fullCname)){
			return new ReflectionClass($fullCname);
		}
		$attrName = str_replace("-", "_", $attrName);
		if(is_callable($attrName)){ // funzione standard di php
			return new BasePhpModifier($attrName);
		}
	}	
	/**
	 * Restituisce la ReflectionClass relativa al plugin "selettore" $attrName 
	 * @param string $attrName
	 * @return ReflectionClass
	 */
	function _defaultSelectors($attrName) {
		$cname = "Selector_".preg_replace("/[^a-z0-9_]/i","_", $attrName); 
		$fullCname = __NAMESPACE__."\\plugins\\selectors\\$cname";
		if(class_exists($fullCname)){
			return new ReflectionClass($fullCname);
		}
	}
	/**
	 * Divide l'uri del tempalte in "file", "selettore" e "query"  da inviare al selettore
	 * @param $templatePath
	 * @return array  0 = file, 1=selettore, 2=query
	 */
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
	/**
	 * Esegue un template
	 * @param string $__file nome dei file da eseguire
	 */
	protected function runCompiled($tpl, $tipo, $query ) {
		$className = $this->getClassFromParts($tpl, $tipo, $query);
		if(!class_exists($className)){
			//die( "Non trovo la classe $className per compilare il file '$file' " );
			try {
				throw new Exception ( "Non trovo la classe $className per compilare il file '$tpl, $tipo, $query' " );	
			} catch (Exception $e) {
				die($e);
			}
			
			
		}
		$ist = new $className($this);
		$ist->addScope($this->getData ());
		$ist->display();
	}
	protected function compile($tpl, $tipo=null, $query=null) {
		$compiledFile = $this->getCacheName($tpl, $tipo, $query);
		
		if(!is_file($tpl)){
			throw new Exception ( "Non trovo il file '$tpl' per poter iniziare la compilazione" );
		}elseif( $this->isChanged($compiledFile, $tpl)) {
			$compiler = new Compiler ( $this );
			$this->setupCompiler($compiler);
			$compiler->compile ( $tpl, $tipo, $query , $compiledFile);
		}
		return $compiledFile;
	}
	/**
	 * Esegui il template e mostra il relativo output
	 * @param string $templatePath
	 */
	public function output($templatePath) {
		try {
			list ($tpl, $tipo, $query) = $this->parseUriParts($templatePath); 
			
			$compiledFile = $this->compile($tpl, $tipo, $query);
			
			$this->runCompiled (  $tpl, $tipo, $query , $compiledFile );
			
		} catch ( DOMException $e ) {
			throw new Exception ( "Errore durante la compilazione del file '$templatePath' (" . $e->getMessage () . ")" , $e->getCode(), $e);
		}
	}
	
	/**
	 * Esegui il template e ritorna il relativo output
	 * @param string $templatePath
	 * @return string
	 */
	public function get($templatePath) {
		ob_start ();
		$this->output ( $templatePath );
		return ob_get_clean ();
	}
	/**
	 * Verifica se il file compilato è campiato rispetto al file template.
	 * @param string $cacheFile
	 * @param string $originalFile
	 */
	protected function isChanged($cacheFile, $originalFile){
		return $this->debug || ! is_file ( $cacheFile ) || filemtime ( $cacheFile ) < filemtime ( $originalFile );
	}
	/**
	 * Ritorna il path del file da usare come cache per il template $tpl
	 * @param string $tpl
	 */
	public function getCacheName($tpl, $tipo, $q) {
		return $this->getCompileDir () . DIRECTORY_SEPARATOR . $this->getClassFromParts($tpl, $tipo, $q).".php";
		
		return $this->getCompileDir () . DIRECTORY_SEPARATOR . preg_replace("/[^a-z0-9_\\-\\.]/i", "_", basename ( $tpl ) ). "_" . md5 ( $tpl.strval ( $this->xmlDeclaration ) . realpath($tpl) ) . ".php";
	}
	public function getClassFromParts($tpl, $tipo='', $q='') {
		return "ATal_".md5("$tpl, $tipo, $q");
	}
	/**
	 * Ritorna la cartella per la cache dei templates
	 * @return string
	 */
	public function getCompileDir() {
		if ($this->compileDir === null) {
			throw new Exception ( 'The compile directory must be set' );
		}
		return $this->compileDir;
	}
	/**
	 * Imposta la cartella per la cache dei templates
	 * @param unknown_type $dir
	 */
	public function setCompileDir($dir) {
		$this->compileDir = rtrim ( $dir, '/\\' );
		if (is_writable ( $this->compileDir ) === false) {
			throw new Exception ( 'The compile directory must be writable, chmod "' . $this->compileDir . '" to make it writable' );
		}
	}
	/**
	 * Rimuovi i file dalla cache
	 * @todo da sistemare
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
	 * Ritorna lo stack corrente
	 * @return array
	 */
	public function &getData() {
		return $this->data;
	}
	/**
	 * aggiungi uno stack
	 * @param array $vars
	 */
	public function addScope(array $vars = array()) {
		unset ( $vars ["this"], $vars ["__file"] );
		$this->scope [] = &$this->data ;
		end ( $this->scope );
		$key = key ( $this->scope );
		foreach ($vars as $k => &$v){
			$this->scope [$key][$k]=&$v;
		}
		$this->data = &$this->scope [$key];
	}
	/**
	 * rimuovi uno stack
	 */
	public function removeScope() {
		array_pop ( $this->scope );
		end ( $this->scope );
		$this->data = &$this->scope [key ( $this->scope )];
	}
	/**
	 * imposta una variabile nello stack corrente
	 * @param $varName
	 * @param $value
	 */
	function assign($varName, $value = null) {
		if ($varName != '') {
			return $this->data [$varName] = $value;
		}
		return null;
	}
	/**
	 * imposta una variabile nello stack corrente
	 * @param string $varName
	 * @param mixed $value
	 */
	public function __set($varName, $value) {
		$this->data [$varName] = $value;
	}
	/**
	 * Recupera una variabile dallo stack corrente
	 * @param string $varName
	 */
	public function &__get($varName) {
		return $this->data [$varName];
	}
	
	/**
	 * assigns values to template variables by reference
	 * @param string $tpl_var the template variable name
	 * @param mixed $value the referenced value to assign
	 */
	function assignByRef($varName, &$value) {
		if ($varName != '') {
			$this->data [$varName] = &$value;
		}
	}
	/**
	 * Svuota tutta lo stack
	 */
	public function clear() {
		$this->scope = array ();
		$this->data = array ();
		$this->addScope ();
	}
}