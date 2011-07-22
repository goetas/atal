<?php
namespace goetas\atal;
namespace goetas\atal\plugins\modifiers;
use goetas\atal\Modifier;
use Exception;
class Modifier_escape extends Modifier {
	function modify($str, array $params=array()){
		return self::escapeValue($str, isset($params[0])?$params[0]:'xml');
	}
	public static function escapeValue($value='', $format='xml'){
		switch($format){
			case 'xml':
				return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
			case 'htmlall':
				return htmlentities((string) $value, ENT_QUOTES, 'UTF-8');
			case 'url':
				return rawurlencode((string) $value);
			case 'urlpathinfo':
				return str_replace('%2F', '/', rawurlencode((string) $value));
			case 'quotes':
				return preg_replace("#(?<!\\\\)'#", "\\'", (string) $value);
			case 'hex':
				$out = '';
				$cnt = strlen((string) $value);
				for ($i=0; $i < $cnt; $i++) {
					$out .= '%' . bin2hex((string) $value[$i]);
				}
				return $out;
			case 'hexentity':
				$out = '';
				$cnt = strlen((string) $value);
				for ($i=0; $i < $cnt; $i++)
					$out .= '&#x' . bin2hex((string) $value[$i]) . ';';
				return $out;
			case 'javascript':
				return strtr((string) $value, array('\\'=>'\\\\',"'"=>"\\'",'"'=>'\\"',"\r"=>'\\r',"\n"=>'\\n','</'=>'<\/'));
			case 'mail':
				return str_replace(array('@', '.'), array('&#160;(AT)&#160;', '&#160;(DOT)&#160;'), (string) $value);
			default:
				throw new Exception('Escape\'s format argument must be one of : xml, htmlall, url, urlpathinfo, hex, hexentity, javascript or mail, "'.$format.'" given.');

		}
	}

}
?>