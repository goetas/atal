<?php
namespace goetas\atal\plugins\modifiers;
use goetas\atal\Modifier;
class Modifier_camelcase extends Modifier {
	function modify($str, array $params=array()){
		return str_replace(" ", "", ucwords(strtr(strtolower($str), "_-", "  ")));
	}
}
