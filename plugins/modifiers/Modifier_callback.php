<?php
namespace goetas\atal\plugins\modifiers;
use goetas\atal\Modifier;
class Modifier_callback extends Modifier {
	function modify($value, array $params = array()) {

		$closure = array_shift($params);
		if(!is_callable($closure)){
			throw new Exception("Il primo parametro passato al modificatore mod-closure deve essere una closure o una callback");
		}

		return  call_user_func_array($closure,array_merge(array($value), $params));
	}
}

