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
	private $filters = array();
	function __construct(Compiler $compiler) {
	}
	/**
	 *
	 * @param $filter callback
	 * @return void
	 */
	function addFilter($filter, $priority = 0) {
		if(is_callable($filter)){
			$this->filters[$priority][]=$filter;
		}else{
			throw new InvalidArgumentException ( "callback non valida per " . __METHOD__ );
		}
	}
	public function getFilters() {
		if(count($this->filters)==1){
			return current($this->filters);
		}
		ksort($this->filters);
		$filters = array();
		foreach ($this->filters as &$gf){
			foreach ($gf as &$f){
				$filters[]=&$f;
			}
		}
		return $filters;
	}
}