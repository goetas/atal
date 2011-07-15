<?php
class ATalModifierPlugin_intval extends ATalModifierPlugin{
	public function modify($str, array $params=array()){
		return intval($str);
	}
}
?>