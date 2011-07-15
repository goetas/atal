<?php
abstract class ATalModifierPlugin extends ATalPlugin {
	/**
	 * Enter description here...
	 *
	 * @var ATal
	 */
	protected $atal;
	public  function __construct(ATal $atal){
		$this->atal=$atal;
	}
	abstract public function modify($value, array $params=array());
}
?>