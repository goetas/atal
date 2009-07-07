<?php

abstract class ATalSelector {
	/**
	 * 
	 * @var ATal_XMLDom
	 */
	protected $dom;
	/**
	 * 
	 * @var ATal
	 */
	protected $tal;
	public function __construct(XMLDom $dom, ATal $atal){
		$this->tal =$atal;
		$this->dom =$dom;
	}
	/**
	 * 
	 * @param $query string
	 * @return array
	 */
	abstract public function select($query);
}

