<?php
namespace goetas\atal\plugins\modifiers;
use goetas\atal\Modifier;
class Modifier_floatval extends Modifier {
	function modify($value, array $params = array()) {
		$value = floatval($value);   
		return number_format ($value , 10 ,  '.' , '');
	}
}
