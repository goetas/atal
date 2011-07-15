<?php
namespace goetas\atal\plugins\modifiers;
use goetas\atal\Modifier;
class Modifier_unescape extends Modifier{
	function modify($str, array $params=array()){
		return self::unescapeValue($str);
	}
	public static function unescapeValue($value='', $format='html'){
		switch($format){
			case 'html':
				return html_entity_decode((string) $value, ENT_QUOTES, 'UTF-8');
			case 'htmlall':
				return html_entity_decode((string) $value, ENT_QUOTES, 'UTF-8');
			case 'url':
				return rawurldecode((string) $value);	
			default:
				throw new \Exception('unEscape\'s format argument must be one of : html, htmlall, url. "'.$format.'" given.');
	
		}
	}

}