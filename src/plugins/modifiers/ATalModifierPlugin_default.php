<?php
class ATalModifierPlugin_default extends ATalModifierPlugin{
	function modify($str, array $params=array()){
		return self::defaultValue($str, $params[0]);
	}
	public static function defaultValue($test, $alternative){
		if($test){
			return $test;
		}else{
			return $alternative;
		}
	}
}
?>