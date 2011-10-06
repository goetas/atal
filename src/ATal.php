<?php
namespace goetas\atal;
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

		$this->finder->addFinder(new finders\Filesystem('.'));

		$this->setup();

		spl_autoload_register (array($this, 'templateLoader'));
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
		foreach ($this->setups as $callback) {
			call_user_func($callback, $this);
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
			foreach ($nds as $nd){
				$len = strlen($nd->data);
				$txt = ltrim($nd->data);
				$nd->deleteData(0, $len);
				$nd->insertData(0, $txt);
			}
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
	protected function runCompiled(Template $t) {
		$className = $this->getClassName($t);

		if(!class_exists($className)){
			throw new Exception ( "Non trovo la classe $className per compilare il file '$t' " );
		}

		$ist = new $className($this);
		$ist->addScope($this->getData ());
		$ist->display();
	}
	public function & getPluginVars() {
		return $this->pluginVars;
	}
	protected function compile(Template $t, $compiledFile = null) {
		if(!$compiledFile){
			$compiledFile = $this->getCacheName($t);
		}
		if( $this->debug || !is_file($compiledFile) || !$this->isFresh($t, filemtime($compiledFile))) {
			$compiler = new Compiler ( $this, $t );
			$this->setupCompiler($compiler);
			$compiler->compile ($this->getTemplate($t) , $compiledFile);
		}
		return $compiledFile;
	}
	/**
	 *
	 * @return Template
	 */
	public function ensureTemplate($template) {
		if(!($template instanceof Template)){
			$template = new Template($template);
		}
		return $template;
	}
	/**
	 * Esegui il template e mostra il relativo output
	 * @param string $templatePath
	 */
	public function output($template) {

		$template = $this->ensureTemplate($template);

		try {
			$this->compile( $template );
			$this->runCompiled ( $template );
		} catch ( DOMException $e ) {
			throw new Exception ( "Errore durante la compilazione di '$template' (" . $e->getMessage () . ")" , $e->getCode(), $e);
		}
	}

	/**
	 * Esegui il template e ritorna il relativo output
	 * @param string $templatePath
	 * @return string
	 */
	public function get($template) {
		ob_start ();
		$this->output ( $template );
		return ob_get_clean ();
	}
	/**
	 * Ritorna il path del file da usare come cache per il template $tpl
	 * @param string $tpl
	 */
	public function getCacheName(Template $t) {
		return $this->getCompileDir () . DIRECTORY_SEPARATOR . $this->getClassName($t).".php";
	}
	public function getClassName(Template $t) {
		return "ATal_".md5($t.$this->getFinder()->getCacheName($t->getBaseName()));
	}
	public function isFresh(Template $t, $current) {
		return $this->getFinder()->isFresh($t->getBaseName(), $current);
	}
	public function getTemplate(Template $t) {
		return $this->getFinder()->getTemplate($t->getBaseName());
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
}