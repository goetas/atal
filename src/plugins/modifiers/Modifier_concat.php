<?php
namespace goetas\atal\plugins\modifiers;
use goetas\atal\Modifier;
class Modifier_concat extends Modifier {
	function modify($str, array $params=array()){
		if(isset($params["before"]) && $params["before"]){
			return $params[0].$params[1].$str;
		}else{
			return $str.$params[1].$params[0];
		}
	}
}
