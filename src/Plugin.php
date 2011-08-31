<?php
namespace goetas\atal;
abstract class Plugin extends  BaseClass implements IPlugin{
	/**
	 *
	 * @var ATal
	 */
	protected $atal;
	public function __construct() {

	}
	function setATal(ATal $atal){
		$this->atal = $atal;
	}
	public function init(array $options=array()){

	}
	public function depends(){
		return array();
	}
}
