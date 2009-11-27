<?php
class ATalModifierPlugin_rewrite extends ATalModifierPlugin {
	function modify($str, array $params=array()){
		return self::rewrite($str, $params);
	}
	public static function rewrite($url, array $params=array()){
		list($modulo, $metodo) = explode('/', $url);
		$modulo = strtolower($modulo);
		
		if ($modulo == 'web'){
			return "{$metodo}.html";
		} else {
			if ($params['id']){
				$params['id'] = "_{$params['id']}";
			}
			if ($params['keys']){
				$params['keys'] = preg_replace('/[^a-z0-9_]+/i', '-', iconv("UTF-8", "ASCII//TRANSLIT", $params['keys']));
			} else {
				$params['keys'] = $modulo;
			}
			return "{$modulo}/{$params['keys']}/{$metodo}{$params['id']}.html";
		}
	}
}