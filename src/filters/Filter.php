<?php
namespace goetas\atal\filters;
use InvalidArgumentException;
use goetas\atal\Compiler;
use goetas\atal\BaseClass;
class Filter extends BaseClass {
	/**
	 * @var $compiler Compiler
	 */
	protected $compiler;
	protected $filters = array();
	function __construct(Compiler $compiler) {
	}
	/**
	 * 
	 * @param $filter callback
	 * @return void
	 */
	function addFilter($filter) {
		if(is_callable($filter)){
			$this->filters[]=$filter;
		}else{
			throw new InvalidArgumentException ( "callback non valida per " . __METHOD__ );
		}
	}
}