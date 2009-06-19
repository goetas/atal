<?php
class ATalModifier {
	protected $params = array();
	/**
	 * @var ATalPluginLoader
	 */
	protected static $modifiers;
	protected $nome;
	/**
	 * @var ATal
	 */
	protected $atal;
	public function __construct(ATal $atal){
		$this->atal = $atal;
		if(!(self::$modifiers instanceof ATalPluginLoader)){
			self::$modifiers = new ATalPluginLoader(dirname(__FILE__).DIRECTORY_SEPARATOR.'plugins'.DIRECTORY_SEPARATOR.'modifiers');
		}
	}
	public function __clone() {
		$this->params=array();
	}
		
	public function add($nome, $plugin){
		self::$modifiers->add($nome, $plugin);
	}
	public function setModifierName($nome){
		$this->nome=$nome;
	}
	/*
	public function addModifier($name, $class){
		if( ($class instanceof ATalExternalModifier) || ($class instanceof ATalModifierPlugin) ){
			$this->modifiers[$name]=$class;
		}elseif(is_object($class)){
			throw new ATalException("Non posso usare ".get_class($class)." come modificatore '$name'");		
		}else{
			$this->loadableModifiers[$name]=$class;
		}
	}
	*/
	public function addParam($str){
		$this->params[]=$str;
	}
	public function addNamedParam($name, $str){
		$this->params[$name]=$str;
	}
	public function runModifier($str){
		return __CLASS__."::modify(\$__tal, '$this->nome' , $str ".($this->params?", array(".ATalCompiler::implodeKeyed($this->params).")":"").")";
	}
	public static function  modify(ATal $atal, $modifier, $value,array $params=array()){
		//$manager = $atal->getModiferManager();
		if(isset(self::$modifiers[$modifier])){
			$mod = self::$modifiers->newInstance($modifier,$atal);
			return $mod->modify($value,  $params);	
		}elseif(is_callable($modifier) || function_exists($modifier)){
			return call_user_func_array($modifier,array_merge(array($value),$params));
		}elseif($modifier=='die'){
			return die($value);
		}else{
			throw new ATalException("Non trovo il modificatore '$modifier'");
		}
	}
	public function loadPlugin(ATalDynamicLoader $loader){
		if($loader instanceof ATalDynamicClass){
			try{
				$ref = new ReflectionClass($loader->getName());
				if(!$ref->isSubclassOf(new ReflectionClass('ATalModifierPlugin'))){
					throw new ATalException('ATalModifierPlugin non valido ('.$loader->getName().')');
				}
				return $ref->newInstance($this->atal);
			}catch(ReflectionException $e){
				return false;
			}	
		}elseif($loader instanceof  ATalDynamicFunction){
			return $loader->getName();
		}
	}
	
}
