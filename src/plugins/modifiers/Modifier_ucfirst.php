<?php
namespace goetas\atal\plugins\modifiers;
use goetas\atal\Modifier;
class Modifier_ucfirst extends Modifier {
	function modify($str, array $params=array()){
		return mb_convert_case(mb_substr($str, 0,1),MB_CASE_UPPER).mb_convert_case(mb_substr($str, 1), MB_CASE_LOWER);
	}
}
