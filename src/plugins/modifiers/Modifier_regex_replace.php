<?php
namespace goetas\atal\plugins\modifiers;
use goetas\atal\Modifier;
class Modifier_regex_replace extends Modifier {
	function modify($str, array $params=array()){
		return preg_replace($params[0], $params[1],(string)$str);
	}
}
