<?php
namespace goetas\atal;
use ReflectionClass;
use InvalidArgumentException;
/**
 *
 * Loader e registro per i plugin
 * @author goetas
 */
abstract class Loader  extends BaseClass{

	protected $plugins = array ();
	protected $reuse = array ();
	protected $defaultPlugins=array();

	protected $initializers = array ();
	/**
	 *
	 * @param $offset
	 * @return IPlugin
	 */
	protected $instances = array();

	function __construct() {

	}
	/**
	 *
	 * Aggiunge un inizializzatore, che verrà applicato a tutti i plugin
	 * @param callback $callback
	 * @throws InvalidArgumentException
	 */
	public function addInitializer( $callback ) {
		if(is_callable($callback)){
			$this->initializers [] = $callback;
		}else{
			throw new InvalidArgumentException ( "Invalid callback type" );
		}
	}
	public function addPlugin($pname, $plugin, $reuse = false, $checkIstance = true) {
		$ist = false;
		if($checkIstance && is_string($checkIstance)){
			$ist = $checkIstance;
		}elseif($checkIstance){
			$ist = $pname;
		}
		$this->plugins [$pname] = $this->makePlugin ( $plugin , $ist);
		$this->reuse [$pname] = $reuse;
	}
	public function addDefaultPlugin( $plugin , $ist = null) {
		$this->defaultPlugins [] = $this->makePlugin ( $plugin, $ist );
	}

	protected function getPlugin($offset, $recycle = false) {
		$recycle = ($recycle || $this->reuse [$offset]);
		if( $recycle && isset($this->instances[$offset])){
			return $this->instances[$offset];
		}elseif(isset($this->plugins [$offset]) && is_callable($this->plugins [$offset])){
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
			throw new PluginException ( "Can't find plugin for '$offset'" );
		}
		if($ist instanceof IPlugin){
			$ist->init();
		}
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
	 * @param string $interface
	 * @return callback
	 */
	protected function makePlugin($plugin, $interface = null) {
		if($interface){
			$interface =  new ReflectionClass($interface);
		}
		if ($plugin instanceof IPlugin) {
			return function ($name)use($plugin, $interface) {
				if($interface && !$interface->isInstance($plugin)){
					throw new PluginException ( get_class($plugin)." must implement ". $interface->getName() ." interface");
				}
				return $plugin;
			};
		}elseif ($plugin instanceof ReflectionClass) {
			return function ($name)use($plugin, $interface) {
				$ist = $plugin->newInstance();
				if($interface && !$interface->isInstance($ist)){
					throw new PluginException ( get_class($ist)." must implement ". $interface->getName() ." interface");
				}
				return $ist;
			};
		} elseif ( $plugin instanceof ClassWrapper) {
			$ist = $this->getInstance ( $plugin->getClassName() );
			if($interface && !$interface->isInstance($ist)){
				throw new PluginException ( get_class($ist)." must implement ". $interface->getName() ." interface");
			}
			return $ist;
		} elseif (is_callable ( $plugin )) {
			$_this = $this;
			return function($name) use ($_this,$plugin, $interface){
				$o  = call_user_func ( $plugin, $name );

				if ($o instanceof IPlugin) {
					$i = $o;
				}elseif ($o instanceof ReflectionClass) {
					$i =  $o->newInstance(); // instance have to implements IPlugin
				} elseif ( $o instanceof ClassWrapper) {
					$f = $_this->getInstance ( $o->getClassName() ); // instance have to implements IPlugin
					$i = $f($name);
				} elseif (is_callable ( $o )) {
					$i = call_user_func ( $o, $name );
				} elseif (is_null ( $o )) {
					throw new PluginException ( "Can't find plugin" );
				}else{
					return $o;
				}

				if ($i instanceof IPlugin) {

					if($interface && !$interface->isInstance($i)){
						throw new PluginException ( get_class($i)." must implement ". $interface->getName() ." interface");
					}

					return $i;
				}
				throw new PluginException ( "Invalid callback return type: " .(is_object($i)?"Class ".get_class($i):gettype($i))." must implement ".__NAMESPACE__."\\IPlugin interface");
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
				throw new PluginException ( "Class $class must implement " . __NAMESPACE__ . "\\IPlugin" );
			}
			$ret = $ref->newInstance ();
			return $ret;
		};
	}

}
