<?php
namespace goetas\atal\plugins\modifiers;
use goetas\atal\Modifier;
class Modifier_if extends Modifier{
	function modify($str, array $params=array()){
		return ((!array_key_exists(0,$params) || $params[0] )?$str:$params[1]);
	}
}
