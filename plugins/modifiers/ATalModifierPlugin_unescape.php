<?php
class ATalModifierPlugin_unescape extends ATalModifierPlugin{
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
				throw new ATalException('unEscape\'s format argument must be one of : html, htmlall, url. "'.$format.'" given.');
	
		}
	}

}