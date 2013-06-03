<?php
namespace goetas\atal;
abstract class EmptyPlugin implements IPlugin{
	function init(array $options=array()){
	}
	public function depends(){
	}
}