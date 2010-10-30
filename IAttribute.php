<?php
namespace goetas\atal;
interface IAttribute extends IPlugin{
	function run(array $params=array(),$content='');
}
