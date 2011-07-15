<?php
namespace goetas\atal\plugins\modifiers;
use goetas\atal\Modifier;
class Modifier_dateformat_locale extends Modifier {
	function modify($value, array $params = array()) {
		if($value instanceof \DateTime ){
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
