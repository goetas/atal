<?php
class ATalPluginLoader implements ArrayAccess, IteratorAggregate {
	protected $loaded=array();
	protected $loadable=array();

	function __construct($path=NULL){
		if($path){
			$this->loadPlugins($path);
		}
	}
	public function loadPlugins($path){
		foreach (new DirectoryIterator($path) as $file){
			if(!$file->isDot() && pathinfo  ( $file->getFilename() , PATHINFO_EXTENSION )=='php'){
				$className =   pathinfo  ( $file->getFilename() , PATHINFO_FILENAME );
				$pluginName =   strtr(substr(  strstr( pathinfo  ( $file->getFilename() , PATHINFO_FILENAME )  ,"_"),1),"_","-");
				$this->add($pluginName ,  new ATalDynamicClass ($className  , $file->getPathname ()  ));
			}
		}
	}
	function newInstance(){
		$params = func_get_args();
		$offset = array_shift($params);
		if($this->loaded[$offset] instanceof ATalPlugin){
			return $this->loaded[$offset];
		}
		$loader = $this->loadable[$offset];
		
		if( $loader instanceof ATalDynamicLoader ){
		
			$loader->loadFile();
			
			$ref = new ReflectionClass($loader->getName());
			
			$ret = $ref->newinstanceArgs( $params );
			
			if($ret){
				foreach($ret->depends() as $pname){
					if(!$this->loaded[$pname] && $this->loadable[$pname]){
						$this->loadable[$pname]->loadFile();
					}
				}
				return $this->loaded[$offset] = $ret;;
			}
		}
		throw new ATalException(__CLASS__." non posso caricare il plugin $offset");
	}
	function getIterator(){
		return new ArrayIterator($this->loaded);
	}
	function offsetExists ($offset){
		return ($this->loaded[$offset] || $this->loadable[$offset]);
	}
 	function offsetGet ($offset){
		return $this->newInstance($offset);
	}
 	function offsetSet ($offset, $value){
		if($value instanceof ATalPlugin){
			$this->loaded[$offset]=$value;
		}else{
			throw new ATalException(__CLASS__." puo contenere solo plugin validi (ATalPlugin), non ".get_class($value));
		}
	}
	function add ($name, ATalDynamicLoader $plugin){
		$this->loadable[$name]=$plugin;
	}
	public function reset() {
		$this->loaded = array();
	}
		
 	function offsetUnset ($offset){
		unset($this->plugins[$offset]);
	}
}
