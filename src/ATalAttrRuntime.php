<?php
class ATalAttrRuntime{	
	protected $attrs;
	protected $name;
	protected $atal;
	public function __construct(ATal $atal){
		$this->atal=$atal;
		$this->attrs = new ATalPluginLoader(dirname(__FILE__).DIRECTORY_SEPARATOR.'plugins'.DIRECTORY_SEPARATOR.'attrs'.DIRECTORY_SEPARATOR.'runtime');
	}
	public function add($nome, $plugin){
		$this->attrs->add($nome, $plugin);
	}
	public function setName($name){
		$this->name=$name;
	}
	public function runAttr(array $params, $content){
		return __CLASS__."::attr(\$__tal, '$this->name' , '". addcslashes($content,"\\'")."' ".($params?", array(".ATalCompiler::implodeKeyed($params).")":"")."  )";
	}
	public static function  attr(ATal $atal, $attr, $content, array $params=array()){
		$manager = $atal->getRuntimeAttrManager();
				
		if (isset($manager->attrs[$attr])){

			$att = $manager->attrs->newInstance($attr,$atal);
			
			return $att->run($params, $content);
			
		}elseif(function_exists('ATalAttrRuntime_'.$attr)){
			return call_user_func_array('ATalAttrRuntime_'.$attr,array($content)+$params);		
		
		}else{
			throw new ATalException("Non trovo un plugin per l'attrubuto '$attr'");
		}
	}
}

