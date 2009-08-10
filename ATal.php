<?php
class ATal {
	const NS ="ATal";
	/**
	 * directory di file comiplati
	 *
	 * @var string
	 */
	protected $compileDir;

	/**
	 * @var ATalCompiler
	 */
	protected $compiler;
	protected $template ;
	/**
	 * @var ATalModifier
	 */
	protected $modifierManager ;
	/**
	 * @var ATalAttrRuntime
	 */
	protected $runtimeAttrManager;
	protected $data=array();

	public $xmlDeclaration=false,$dtdDeclaration=true;

	public $debug=0;

	protected $scope=array();

	public function __construct($compileDir = null){

		$this->addScope(array(
			'now'		=>	$_SERVER['REQUEST_TIME'],
		));
		if ($compileDir !== null) {
			$this->setCompileDir($compileDir);
		}


		$this->modifierManager = new ATalModifier($this);
		$this->runtimeAttrManager = new ATalAttrRuntime($this);

		$this->compiler = new ATalCompiler($this);

		//$this->compiler->addPostFilter(array(__CLASS__,'makeHXTML'));
		$this->addSelector("id",'ATalIdSelector');
		$this->addSelector("childid",'ATalChildIdSelector');
		$this->addSelector("xpath",'ATalXPathSelector');
		$this->addSelector("css",'ATalCssSelector');
	}

	function __clone(){
		$this->clear();
		$sel = $this->compiler->getSelectors();
		$this->compiler = new ATalCompiler($this);
		foreach ($sel as $name => $class) {
			$this->addSelector($name,$class);
		}
	}
	/*
	public static function makeHXTML($str){
		return $str; //preg_replace("//","",$str);
	}
*/
	public function getModiferManager(){
		return $this->modifierManager;
	}
	public function getRuntimeAttrManager(){
		return $this->runtimeAttrManager;
	}

	protected static $paths=array();

	public static function autoLoadAdd($cname, $path){
		if(is_file($path)){
			self::$paths[$cname]=realpath($path);
		}else{
			throw new ATalException(__CLASS__." persorso $path non valido per la classe $cname");
		}
	}
	public static function autoLoad($cname){
		$pname = str_replace("-","_",$cname);

		if(in_array($cname,array("XMLAble","XMLDom","XMLDomElement","XPath"))){
			$file = 'xml'.DIRECTORY_SEPARATOR.$pname.'.php';
		}elseif(\ambient\contains($cname,"Selector") && $cname!='ATalSelector' ){
			$file = 'selectors'.DIRECTORY_SEPARATOR.$pname.'.php';
		}else{
			$file = $pname.'.php';
		}
		if(is_readable(dirname(__FILE__).DIRECTORY_SEPARATOR.$file)){
			include (dirname(__FILE__).DIRECTORY_SEPARATOR.$file);
			return;
		}
		if(is_readable(self::$paths[$cname])){
			include (self::$paths[$cname]);
		}
	}
	protected function runCompiled($__file){
		extract($this->getData());
		$__tal=$this;
		include $__file;
	}
	public function output($tplFile){
		list($tpl, $query)=explode('#',$tplFile,2);
		$mch=array();
		if(strlen($query) && preg_match("/^([a-z]+)\s*:(.+)$/i",$query,$mch)){
			$tipo  = $mch[1];
			$query = $mch[2];
		}elseif(strlen($query)){
			$tipo  = "id";
		}
		if(!is_file($tpl)){
			throw new ATalException("non trovo il file '$tpl'");
		}
		$this->template = realpath($tpl);
		try {
			$compiledFile = $this->compiler->compile($this->template,$tipo?$tipo:false,$tipo?$query:false);
		}catch (DOMException $e){
			throw new ATalException("Errore durante la compilazione del file '$this->template' (".$e->getMessage().")");
		}

		$this->runCompiled($compiledFile);
	}
	public function get($tpl){
		ob_start();
		$this->output($tpl);
		return ob_get_clean();
	}
	function addCompiledAttr($name, ATalDynamicClass $plugin){
		$this->compiler->addCompiledAttr( $name, $plugin);
	}
	public function addRuntimeAttr($name, ATalDynamicClass $plugin){
		$this->runtimeAttrManager->add( $name, $plugin);
	}

	public function addModifier($name, $plugin){
		if( $plugin instanceof ATalDynamicLoader ){
			$this->modifierManager->add( $name, $plugin);
		}elseif(is_string($plugin)){
			$this->modifierManager->add( $name, new ATalDynamicFunction($plugin));
		}else{
			throw new ATalException(__CLASS__." modifier $name non valido");
		}
	}

	public function setDefaultModifier($plugin){
		$this->compiler->setDefaultModifier($plugin);
	}
	public function unsetDefaultModifier(){
		$this->compiler->unsetDefaultModifier();
	}
	public function addSelector($name, $class){
		$this->compiler->addSelector($name, $class);
	}

	public function getCompileDir(){
		if ($this->compileDir === null) {
			$this->setCompileDir(dirname(__FILE__).DIRECTORY_SEPARATOR.'compiled');
		}

		return $this->compileDir;
	}
	public function setCompileDir($dir){
		$this->compileDir = rtrim($dir, '/\\');
		if (is_writable($this->compileDir) === false) {
			throw new ATal_Exception('The compile directory must be writable, chmod "'.$this->compileDir.'" to make it writable');
		}
	}
	public function getTemplate(){
		return $this->template;
	}
	public function clearCache($olderThan=-1){
		$cacheDirs = new RecursiveDirectoryIterator($this->getCompileDir());
		$cache = new RecursiveIteratorIterator($cacheDirs);
		$expired = time() - $olderThan;
		$count = 0;
		foreach ($cache as $file) {
			if ($cache->isDot() || $cache->isDir() || substr($file, -5) !== '.html') {
				continue;
			}
			if ($cache->getCTime() < $expired) {
				$count += unlink((string) $file) ? 1 : 0;
			}
		}
		return $count;
	}

	public function &getData(){
		return $this->data;
	}

	public function addScope(array $vars=array()){
		unset($vars["this"],$vars["__file"]);
		$this->scope[]=array_merge($this->data,$vars);
		end($this->scope);
		$this->data=&$this->scope[key($this->scope)];
	}
	public function removeScope(){
		array_pop($this->scope);
		end($this->scope);
		$this->data=&$this->scope[key($this->scope)];
	}
    function assign($varName, $value = null){
		if ($varName != ''){
			return $this->data[$varName] = $value;
		}
		return NULL;
    }
	public function __set($varName, $value){
		$this->data[$varName] = $value;
	}
	public function & __get($varName){
		return $this->data[$varName];
	}

    /**
     * assigns values to template variables by reference
     *
     * @param string $tpl_var the template variable name
     * @param mixed $value the referenced value to assign
     */
    function assignByRef($varName, &$value){
        if ($varName != ''){
            $this->data[$varName] = &$value;
		}
    }
	public function clear() {
		$this->scope = array();
		$this->data = array();
		$this->addScope();
	}

    /**
     * appends values to template variables
     *
     * @param array|string $tpl_var the template variable name(s)
     * @param mixed $value the value to append
     */
    function append($varName, $value=null, $merge=false){
            if ($varName != '' && isset($value)) {
                if(!@is_array($this->data[$varName])) {
                    settype($this->data[$varName],'array');
                }
                if($merge && is_array($value)) {
                    foreach($value as $_mkey => $_mval) {
                        $this->data[$varName][$_mkey] = $_mval;
                    }
                } else {
                    $this->data[$varName][] = $value;
                }
            }
    }
}
spl_autoload_register(array('ATal','autoLoad'));
