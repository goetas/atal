<?php
class ATalModifierPlugin_implode extends ATalModifierPlugin{
	function modify($str, array $params=array()){
		return is_array($str)?implode($params[0],$str):'';
	}
}
