<?php
namespace goetas\atal\plugins\modifiers;
use goetas\atal\Modifier;
class Modifier_translate_var extends Modifier {
	function modify($value, array $params = array()) {
		return $this->tal->getServices()->service('goetas\\atal\\plugins\\services\\ITranslate')->translate($value,array($params));
	}
}