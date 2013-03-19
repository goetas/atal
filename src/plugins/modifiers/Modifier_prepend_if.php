<?php
namespace goetas\atal\plugins\modifiers;
use goetas\atal\Modifier;
class Modifier_prepend_if extends Modifier{
	function modify($str, array $params=array()){
		return ($str && (!array_key_exists(1,$params) || $params[1] )?$params[0]:"").$str;
	}
}
