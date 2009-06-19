<?php
class ATalModifierPlugin_first extends ATalModifierPlugin{
	function modify($str, array $params=array()){
		return self::firstValue($str, $params[0]);
	}
	public static function firstValue($value, $suggest){
		if(( is_array($value) ||  is_object($value)) && strlen(trim($value[$suggest]))>0){
			return trim($value[$suggest]);
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
?>