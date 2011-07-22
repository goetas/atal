<?php
namespace goetas\atal\plugins\modifiers;
use goetas\atal\Modifier;
class Modifier_strip_tags extends Modifier{
	function modify($str, array $params=array()){

		$str = preg_replace('~<br\\s*/>~mi', " ", $str);

		$str = strip_tags($str, $params[0]);
		if(!$params[0]){
			$str = html_entity_decode ( $str ,ENT_COMPAT ,'UTF-8');
		}

		return $str;
	}
}