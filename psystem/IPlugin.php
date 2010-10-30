<?php
namespace goetas\pluginsys;
interface IPlugin{
	function init(array $options=array());
	public function depends();
}
