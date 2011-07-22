<?php
namespace goetas\atal\plugins\modifiers;
use goetas\atal\Modifier;
class Modifier_implode extends Modifier{
	function modify($str, array $params=array()){
		if ($params[0]===null){
			$params[0] = ', ';
		}
		return is_array($str)?implode($params[0], $str):'';
	}
}
