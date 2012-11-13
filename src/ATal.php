<?php
namespace goetas\atal;
use goetas\atal\extensions\fixcdata\FixCdata;

use DOMException;
use InvalidArgumentException;
use ReflectionClass;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use goetas\xml;
/**
 * SVN revision @@version@@
 * PHP Template engine
 * @author goetas
 *
 */
class ATal extends DataContainer{
	const NS = "ATal";
	/**
	 * Directory dei file comiplati
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
	 * insieme di callback per configurare a runtime il compilatore
	 * @var array
	 */
	protected $compilerSetups = array();
	/**
	 * insieme di callback per configurare atal
	 * @var array
	 */
	protected $setups = array();

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
	/**
	 *
	 * @var \goetas\atal\finders\Aggregate
	 */
	protected $finder;
	/**
	 * @var array
	 */
	protected $pluginVars = array();
	/**
	 * @var array
	 */
	protected static $templateInfo = array();
	/**
	 * Array di callback per caricare i template dalle possibili cache
	 * @var array
	 */
	protected $compiledTemplateLoaders = array();
	/**
	 *
	 * @param string $compileDir cartella da usare per la cache dei templates compilati
	 * @param string $defaultModifier pre-modificatore di default. "escape" è il modificatore di default
	 */
	public function __construct($compileDir = null, $defaultModifier='escape') {

		parent::__construct();

		if ($compileDir !== null) {
			$this->setCompileDir ( $compileDir );
		}

		$this->modifiers = new loaders\Modifiers ( $this , $defaultModifier);
		$this->services = new loaders\Services ( $this );
		$this->finder = new finders\Aggregate();

		$this->setup();
		
		spl_autoload_register (array($this, '_compiledTemplateLoader'));
	}
	
	public function addExtension(IExtension $extension) {
		$extension->setup($this);
		$this->addCompilerSetup(array($extension, 'setupCompiler'));
	}
	
	/**
	 *
	 * @return \goetas\atal\finders\Aggregate
	 */
	public function getFinder() {
		return $this->finder;
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
		
		$this->addCompiledTemplateLoader(array($this,'_defaultCompiledTemplateLoader') );
		
		$this->finder->addFinder(new finders\Filesystem('.'));
		
		$this->modifiers->addDefaultPlugin( array($this,'_defaultModifiers') , __NAMESPACE__.'\IModifier');
		$this->addExtension(new FixCdata());
		foreach ($this->setups as $callback) {
			call_user_func($callback, $this);
		}
	}
	public function addCompiledTemplateLoader($callback) {
		if(is_callable($callback)){
			$this->compiledTemplateLoaders[]=$callback;
		}else{
			throw new InvalidArgumentException ( "Callback non valida per " . __METHOD__ );
		}
	}
	
	public function addCompilerSetup($callback) {
		if(is_callable($callback)){
			$this->compilerSetups[]=$callback;
		}else{
			throw new InvalidArgumentException ( "Callback non valida per " . __METHOD__ );
		}
	}
	
	public function addSetup($callback) {
		if(is_callable($callback)){
			$this->setups[]=$callback;
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

		$compiler->getPreXmlFilters()->addFilter(array($this,'_handleT'));

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
			$nds = $node->query("text()");
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
				return "<?php ";
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
	public function _compiledTemplateLoader($class){
		$__tal_template_info = self::$templateInfo[$class];
		foreach ($this->compiledTemplateLoaders as $loader){ 
			call_user_func($loader, $class, $__tal_template_info);
			if(class_exists($class, false)){
				return;
			}
		}
	}
	public function _defaultCompiledTemplateLoader($class, $__tal_template_info) {
		if(preg_match("/^ATal_[a-f0-9]{32}$/", $class)){
			require $this->getCompileDir()."/". $class.".php";
		}
	}
	/**
	 * Esegue un template
	 * @param string $__file nome dei file da eseguire
	 */
	protected function runCompiled($className, TemplateRef $templateRef, IFinder $finder) {
		if(!class_exists($className)){
			throw new Exception ( "Non trovo la classe $className" );
		}
		$ist = new $className($this, $templateRef, $finder);
		$ist->addScope($this->getData ());
		$ist->display();
	}
	
	public function & getPluginVars() {
		return $this->pluginVars;
	}
	/**
	 * @param TemplateRef $template
	 * @param string $cachedFilename
	 * @return string Class name to run
	 */
	protected function compile(TemplateRef $templateRef) {

		$cachedFilename = $this->getCachePath($templateRef);
		
		$className = $this->getClassName($templateRef);
		$finderRef = null;
		if($this->needsRecompile($templateRef, $cachedFilename, $finderRef)) {
			$compiler = new Compiler ( $this, $this->finder->getTemplate($templateRef, $finderRef) );
			$this->setupCompiler($compiler);
			$compiler->compile ($cachedFilename, $className);
		}
		return self::$templateInfo[$className]=array(
			"finder" => $finderRef,
			"class" => $className,
			"templateRef" => $templateRef
		);
	}
	
	protected function needsRecompile(TemplateRef $template, $cachedFilename, &$finderRef){
		if($this->debug){
			return true;
		}
		$stat = @stat($cachedFilename);
		return  !$stat || !$this->getFinder()->isFresh($template, $stat["mtime"], $finderRef);
	}
	
	/**
	 *
	 * @return TemplateRef
	 */
	public function convertTemplateName($template, TemplateRef $parent = null) {
		return new TemplateRef($this, $template, $parent);
	}
	/**
	 * Esegui il template e mostra il relativo output
	 */
	public function outputTemplate(TemplateRef $template) {
		try {
			$info = $this->compile( $template );
	
			$this->runCompiled ( $info["class"], $template, $info["finder"] );
		} catch ( DOMException $e ) {
			throw new Exception ( "Errore durante la compilazione di '$template' (" . $e->getMessage () . ")" , $e->getCode(), $e);
		}
	}
	/**
	 * Esegui il template e mostra il relativo output
	 * @param string $templatePath
	 */
	public function output($templatePath) {
		$template = $this->convertTemplateName($templatePath);
		$this->outputTemplate($template);
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
	 * Ritorna il path del file da usare come cache per il template $tpl
	 * @return string
	 */
	public function getCachePath(TemplateRef $template) {
		return $this->getCompileDir () . DIRECTORY_SEPARATOR . $this->getClassName($template).".php";
	}
	
	public function getClassName(TemplateRef $template) {
		return "ATal_".md5($template.$this->getFinder()->getCacheName($template));
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
}