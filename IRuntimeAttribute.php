<?php
namespace goetas\atal;
Interface IRuntimeAttribute extends IPlugin{
	function run(array $params, $content);
}
