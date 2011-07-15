<?php
class ATalModifierPlugin_dateformat_locale extends ATalModifierPlugin {
	function modify($value, array $params = array()) {
		if($value instanceof DateTime ){
			$time =  $value->format("U");
		}elseif(preg_match( '#^\d+$#', $value )){
			$time =  $value ;
		}elseif (!is_null($value)){
			$time = strtotime( $value );
		}else{
			$time = time();
		}
		return strftime( $params[0], $time );
	}
}
