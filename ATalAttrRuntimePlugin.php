<?php
abstract class ATalAttrRuntimePlugin extends ATalPlugin{
	protected $atal;
	public  function __construct(ATal $atal){
		$this->atal=$atal;
	}
	abstract public function run(array $params=array(),$content='');
}
?>