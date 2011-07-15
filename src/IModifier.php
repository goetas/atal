<?php
namespace goetas\atal;
interface IModifier extends IPlugin {
	function modify($value, array $params=array());
}
