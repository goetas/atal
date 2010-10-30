<?php
namespace goetas\atal\plugins\modifiers;
use goetas\atal\Modifier;
class Modifier_rewrite extends Modifier {
	function modify($str, array $params=array()){
		return self::rewrite($str, $params);
	}
	public static function rewrite($url, array $params=array()){
		list($url, $request) = explode('?', $url);

		list($modulo, $metodo) = explode('/', $url);
		$modulo = strtolower($modulo);

		if ($params['id']){
			$p = "_{$params['id']}";
		}
		if ($params['keys']){
			$params['keys'] = trim(str_replace($modulo, '', preg_replace('/[^a-z0-9_]+/i', '-', strtolower(iconv("UTF-8", "ASCII//TRANSLIT", $params['keys'])))),"-");
		}
		if ($modulo == 'web' && !$params['keys']){
			if($params['id']){
				$url = "{$metodo}{$p}.html";
			}else{
				$url = "{$metodo}.html";
			}
		} else {
			$url = "{$modulo}/".(($params['keys'])?"{$params['keys']}/":'')."{$metodo}{$p}.html";
		}
		return $url.(($request)?"?{$request}":'');
	}
}