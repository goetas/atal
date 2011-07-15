<?php
class ATalModifierPlugin_remove_attribute extends ATalModifierPlugin{
	function modify($str, array $params=array()){
		$attrlist = implode("|",array_map(array(__CLASS__,'quoteattr'),$params[0]));
		$pattern = "~(($attrlist)=\"[^\"]*\")|(($attrlist)='[^']*')~i";
		return preg_replace($pattern, "", $str);
	}
	public static function quoteattr($attr) {
		return preg_quote($attr,"~");
	}

}
