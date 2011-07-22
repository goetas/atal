<?php
namespace goetas\atal\plugins\modifiers;
use goetas\atal\Modifier;
class Modifier_default extends Modifier{
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