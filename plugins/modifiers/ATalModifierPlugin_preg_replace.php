<?php
class ATalModifierPlugin_preg_replace extends ATalModifierPlugin {
	function modify($value, array $params = array()) {
		return preg_replace( $params [0], (string)$params [1], $value );
	}
}

