<?php
namespace goetas\atal\plugins\modifiers;
use goetas\atal\Modifier;
class Modifier_strtolower extends Modifier{
	function modify($str, array $params=array()){
		if(function_exists('mb_convert_case')){
			return mb_convert_case($str, MB_CASE_LOWER, 'UTF-8');
		}else{
			return strtolower($str);
		}
	}
}
