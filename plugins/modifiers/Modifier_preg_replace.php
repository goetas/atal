<?php
namespace goetas\atal\plugins\modifiers;
use goetas\atal\Modifier;
class Modifier_preg_replace extends Modifier {
	function modify($value, array $params = array()) {
		return preg_replace( $params [0], (string)$params [1], $value );
	}
}

