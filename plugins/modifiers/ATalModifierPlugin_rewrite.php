<?php
class ATalModifierPlugin_rewrite extends ATalModifierPlugin {
	function modify($str, array $params=array()){
		return self::rewrite($str, $params);
	}
	public static function rewrite($url, array $params=array()){
		list($url, $request) = explode('?', $url);
		
		list($modulo, $metodo) = explode('/', $url);
		$modulo = strtolower($modulo);
		
		if ($modulo == 'web'){
			$url = "{$metodo}.html";
		} else {
			if ($params['id']){
				$params['id'] = "_{$params['id']}";
			}
			if ($params['keys']){
				$params['keys'] = str_replace($modulo, '', preg_replace('/[^a-z0-9_]+/i', '-', strtolower(iconv("UTF-8", "ASCII//TRANSLIT", $params['keys']))));
			}
			$url = "{$modulo}/".(($params['keys'])?"{$params['keys']}/":'')."{$metodo}{$params['id']}.html";
		}
		return $url.(($request)?"?{$request}":'');
	}
}