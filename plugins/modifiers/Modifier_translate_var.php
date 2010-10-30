<?php
namespace goetas\atal\plugins\modifiers;
use goetas\atal\Modifier;
class Modifier_translate_var extends Modifier {
	function modify($value, array $params = array()) {
		return \ambient\i18n\I18nClass::t( $value, $params );
	}
}