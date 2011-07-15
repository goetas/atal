<?php
/**
 * Esempio
 * <?php
 * $atal->articleTitle='Two Sisters Reunite after Eighteen Years at Checkout Counter.';
* ?>
* 
* where template is:
* 
* {$articleTitle}
* {$articleTitle|truncate}
* {$articleTitle|truncate:30}
* {$articleTitle|truncate:30:""}
* {$articleTitle|truncate:30:"---"}
* {$articleTitle|truncate:30:"":true}
* {$articleTitle|truncate:30:"...":true}
* {$articleTitle|truncate:30:'..':true:true}
* 
* This will output:
* 
* Two Sisters Reunite after Eighteen Years at Checkout Counter.
* Two Sisters Reunite after Eighteen Years at Checkout Counter.
* Two Sisters Reunite after...
* Two Sisters Reunite after
* Two Sisters Reunite after---
* Two Sisters Reunite after Eigh
* Two Sisters Reunite after E...
* Two Sisters Re..ckout Counter.
 *
 */
class ATalModifierPlugin_truncate extends ATalModifierPlugin {
	function modify($string, array $params = array()) {
		//$string, $length = 80, $etc = '...',  $break_words = false, $middle = false){
		$length =  isset($params [0])?$params [0]:80;
		$etc = isset($params [1])?$params [1]:'...';
		$break_words = isset($params [2])?$params [2]:false;
		$middle = isset($params [3])?$params [3]:false;
		
		if($length == 0){ 
			return '';
		}
		
		if(strlen( $string ) > $length){
			$length -= min( $length, strlen( $etc ) );
			if(! $break_words && ! $middle){
				$string = preg_replace( '/\s+?(\S+)?$/', '', substr( $string, 0, $length + 1 ) );
			}
			if(! $middle){
				return substr( $string, 0, $length ) . $etc;
			}else{
				return substr( $string, 0, $length / 2 ) . $etc . substr( $string, - $length / 2 );
			}
		}else{
			return $string;
		}
	}
}