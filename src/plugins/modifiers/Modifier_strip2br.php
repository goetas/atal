<?php
namespace goetas\atal\plugins\modifiers;
use goetas\atal\Modifier;
class Modifier_strip2br extends Modifier {
	function modify($str, array $params=array()){

		if (!isset($params['new_lines'])){
			$str = preg_replace('~[\\n\\r]+~m', ' ', $str);
		}

		$str = preg_replace('~\\n*<br\\s*/>\\n*~mi', "\n", $str);
		/*
		if($params[0]){
			$params[0] = implode ( array_map ( function ($v) {
				return "<$v>";
			}, explode ( ",", $params[0] ) ) );
		}
		$str = strip_tags($str, $params[0]);
		*/

		if(isset($params['allow'])){
			$allow = implode("|",explode(",",$params['allow']));

			$str = preg_replace_callback("~<($allow)[\\s]*>|</($allow)>~i", function($mch){
				
				return str_replace(array("<", ">"), array("[[[[~", "~]]]]"),  $mch[0]);
				
			}, $str);
		}
		
		
		$str = strip_tags($str, $allow);
		$str = html_entity_decode($str, ENT_QUOTES, 'UTF-8');

		$str = preg_replace('~^[\p{Z}\t]+|[\p{Z}\t]+$~miu', "", $str);
		$str = trim($str);

		if ($params['truncate'] !== null){
			$length = $params ['truncate'];
			$etc = isset($params ['truncate-etc'])?$params ['truncate-etc']:'...';
			$break_words = isset($params ['truncate-break-words'])?$params ['truncate-break-words']:false;
			$middle = isset($params ['truncate-middle'])?$params ['truncate-middle']:false;
			$str = Modifier_truncate::truncate($str, $length, $etc, $break_words, $middle);
		}
		$str = htmlspecialchars((string) $str, ENT_QUOTES, 'UTF-8');
		$str = nl2br($str);
		
		if(isset($params['allow'])){
			$str = str_replace( array("[[[[~", "~]]]]"),array("<", ">"),  $str);
		}
		return $str;
	}
}