<?php
class ATalModifierPlugin_rewrite extends ATalModifierPlugin {
	function modify($str, array $params=array()){
		return self::rewrite($str, $params);
	}
	public static function rewrite($url, array $params=array()){
		$p = explode('/', $url);
		
		if ($params['id']){
			$params['id'] = "_{$params['id']}";
		}
		if ($params['keys']){
			$params['keys'] = preg_replace('/[^a-z0-9_]+/i', '-', iconv("UTF-8", "ASCII//TRANSLIT", $params['keys']));
		} else {
			$params['keys'] = $p[0];
		}
		
		return "{$p[0]}/{$params['keys']}/{$p[1]}{$params['id']}.html";
	}
}
?>