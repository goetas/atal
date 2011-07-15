<?php
class ATalModifierPlugin_nl2br extends ATalModifierPlugin{
	function modify($str, array $params=array()){
		return nl2br($str);
	}
}
?>