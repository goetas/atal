<?php
class ATalModifierPlugin_translate_var extends ATalModifierPlugin {
	function modify($value, array $params = array()) {
		return \ambient\i18n\I18nClass::t( $value, $params );
	}
}