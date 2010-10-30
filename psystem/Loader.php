<?php
namespace goetas\pluginsys;
use ReflectionClass;
use RuntimeException;
use InvalidArgumentException;
abstract class Loader  extends BaseClass{
	
	protected $plugins = array ();
	protected $defaultPlugins=array();
	
	protected $initializers = array ();
	function __construct() {
	
	}
	public function addInitializer( $callback) {
		if(is_callable($callback)){
			$this->initializers [] = $callback;
		}else{
			throw new InvalidArgumentException ( "Invalid callback type" );
		}
	}
	public function addPlugin($pname, $plugin) {
		$this->plugins [$pname] = $this->makePlugin ( $plugin );
	}
	public function addDefaultPlugin( $plugin ) {
		$this->defaultPlugins [] = $this->makePlugin ( $plugin );
	}	
	/**
	 * 
	 * @param $offset
	 * @return IPlugin
	 */
	protected $instances = array();
	protected function getPlugin($offset, $recycle = false) {
		if( $recycle && $this->instances[$offset]){
			return $this->instances[$offset];
		}elseif(is_callable($this->plugins [$offset])){
			$ist = call_user_func ( $this->plugins [$offset],$offset );
		}else{
			foreach (array_reverse($this->defaultPlugins) as $callback) {
				$ist = call_user_func ( $callback ,$offset);
				if($ist){
					break;
				}
			}
		}
		if(!$ist){
			throw new RuntimeException ( "Can't find plugin for '$offset'" );
		}
		
		$ist->init();
		foreach($this->initializers as $initializer){
			call_user_func($initializer,$ist);
		}
		if( $recycle ){
			$this->instances[$offset]=$ist;
		}
		return $ist;
	}
	/**
	 * 
	 * @param mixed $plugin
	 * @return callback
	 */
	protected function makePlugin($plugin) {
		if ($plugin instanceof IPlugin) {
			return function ($name)use($plugin) {
				return $plugin;
			};
		}elseif ($plugin instanceof ReflectionClass) {
			return function ($name)use($plugin) {
				return $plugin->newInstance();
			};			
		} elseif ( $plugin instanceof ClassWrapper) {
			return $this->getInstance ( $plugin->getClassName() );
		} elseif (is_callable ( $plugin )) {
			$_this = $this;
			return function($name)use($_this,$plugin){
				$o  = call_user_func ( $plugin, $name );
				if ($o instanceof IPlugin) {
					return $o;
				}elseif ($o instanceof ReflectionClass) {
					return $o->newInstance();
				} elseif ( $o instanceof ClassWrapper) {
					$f = $_this->getInstance ( $o->getClassName() );
					return $f($name);
				} elseif (is_callable ( $o )) {
					return $o;
				}else{
					throw new InvalidArgumentException ( "Invalid callback return type " .var_export($o,1));
				}
			};
		} else {
			throw new InvalidArgumentException ( "Invalid arg #2" );
		}
	}
	/**
	 * 
	 * @param string $class
	 * @return callback
	 */
	function getInstance($class) {
		return function ($name)use($class) {
			$ref = new ReflectionClass ( $class );
			if (! $ref->isSubclassOf ( __NAMESPACE__ . "\\IPlugin" )) {
				throw new InvalidArgumentException ( "Class $class must implement " . __NAMESPACE__ . "\\IPlugin" );
			}
			$ret = $ref->newInstance ();
			return $ret;
		};
	}

}
