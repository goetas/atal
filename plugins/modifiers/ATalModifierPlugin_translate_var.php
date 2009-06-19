<?php
class ATalModifierPlugin_translate_var extends ATalModifierPlugin {
	function modify($value, array $params = array()) {
		return I18nClass::t( $value, $params, $this->atal->getTemplate() );
	}
}