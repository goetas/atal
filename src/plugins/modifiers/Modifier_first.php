<?php
namespace goetas\atal;
namespace goetas\atal\plugins\modifiers;
use goetas\atal\Modifier;
class Modifier_first extends Modifier{
	function modify($str, array $params=array()){
		return self::firstValue($str, $params[0], $params[1]);
	}
	public static function firstValue($value, $suggest, $else = null){
		if(!is_null($suggest) && ( is_array($value) ||  is_object($value)) && strlen(trim($value[$suggest]))>0){
			return trim($value[$suggest]);
		}elseif(!is_null($else) && ( is_array($value) ||  is_object($value)) && strlen(trim($value[$else]))>0){
			return trim($value[$else]);
		}elseif(is_array( $value ) || ($value instanceof Iterator ) || ($value instanceof IteratorAggregate )){
			foreach ($value as $val){
				if(strlen(trim($val))){
					return trim($val);
				}
			}
			return '';
		}else{
			return '';
		}
	}
}
