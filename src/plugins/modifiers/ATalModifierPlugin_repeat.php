<?php
class ATalModifierPlugin_repeat extends ATalModifierPlugin{
	function modify($str, array $params=array()){
		return str_repeat((string)$str,intval((string)$params[0]));
	}
}
