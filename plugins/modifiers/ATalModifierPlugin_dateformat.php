<?php
class ATalModifierPlugin_dateformat extends ATalModifierPlugin {
	function modify($value, array $params = array()) {
		if($value instanceof DateTime ){
			return $value->format($params[0]);
		}elseif(preg_match( '#^\d+$#', $value )){
			return date( $params[0], $value );
		}elseif (!is_null($value)){
			$value = strtotime( $value );
			return date( $params[0], $value );
		}else{
			return '';
		}
	}
}
