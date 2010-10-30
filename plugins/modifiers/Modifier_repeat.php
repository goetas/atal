<?php
namespace goetas\atal\plugins\modifiers;
use goetas\atal\Modifier;
class Modifier_repeat extends Modifier{
	function modify($str, array $params=array()){
		return str_repeat((string)$str,intval((string)$params[0]));
	}
}
