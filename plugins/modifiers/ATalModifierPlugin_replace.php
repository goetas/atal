<?php
class ATalModifierPlugin_replace extends ATalModifierPlugin{
	function modify($str, array $params=array()){
		return str_replace($params[0],$params[1],(string)$str);
	}
}
