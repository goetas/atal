<?php
namespace goetas\atal\plugins\modifiers;
use goetas\atal\Modifier;
class Modifier_replace extends Modifier{
	function modify($str, array $params=array()){
		if ($params['casesensitive'] === false){
			return str_ireplace($params[0], $params[1],(string)$str);
		} else {
			return str_replace($params[0], $params[1],(string)$str);
		}
	}
}
