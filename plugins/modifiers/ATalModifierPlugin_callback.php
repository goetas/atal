<?php
class ATalModifierPlugin_callback extends ATalModifierPlugin {
	function modify($value, array $params = array()) {

		$closure = array_shift($params);
		if(!is_callable($closure)){
			throw new ATalException("Il primo parametro passato al modificatore mod-closure deve essere una closure o una callback");
		}

		return  call_user_func_array($closure,array_merge(array($value), $params));
	}
}

