<?php
namespace goetas\atal\plugins\modifiers;
use goetas\atal\Modifier;
class Modifier_formatgeo extends Modifier {
	function modify($radian, array $params=array()){
		$grad = rad2deg(floatval($radian));
		if($params[0]=="degsex"){
			return self::totexxt( $grad/60, 0 , $params[1]=="LAT");
		}else{
			return $grad;
		}
	}
	static function  totexxt( $coordinate, $decSeconds , $h=true) {

		// Evitem que hi hagi menys de 0 decimals
		if ($decSeconds < 0)
			$decSeconds = 0;

		$decimal = pow ( 10, $decSeconds );

		// Determinem el signe de la coordenada

		if($h){
			$di=($coordinate >=0)?"N":"S";
		}else{
			$di=($coordinate >=0)?"E":"W";
		}

		// Trobem els graus, minuts i segons
		$degrees = abs ( $coordinate );
		$minutes = ($degrees - floor ( $degrees )) * 60;
		$seconds = ($minutes - floor ( $minutes )) * 60;
		$seconds = round ( $seconds * $decimal ) / $decimal;

		// Els arrodonim per despreciar pèrdues de decimals
		if ($seconds >= 60) {
			$minutes ++;
			$seconds -= 60;
		}
		if ($minutes >= 60) {
			$degrees ++;
			$minutes -= 60;
		}

		// Muntem el text de la coordenada
		$text = floor ( $degrees ) . "° " . floor ( $minutes ) . "' " . $seconds . "\" ".$di;

		return $text;
}




}
?>