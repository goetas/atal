<?php
namespace goetas\atal;
abstract class EmptyPlugin implements IPlugin{
	protected $atal;
	function init(array $options=array()){
	}
	public function depends(){
	}
	function setATal(ATal $atal){
		$this->atal = $atal;
		return $this;
	}
}