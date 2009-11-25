<?php
class ATalModifierPlugin_implode extends ATalModifierPlugin{
	function modify($str, array $params=array()){
		return implode($params[0],$str);
	}
}
