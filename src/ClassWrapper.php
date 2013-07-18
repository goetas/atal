<?php
namespace goetas\atal;
use ReflectionClass;
use InvalidArgumentException;
class ClassWrapper extends BaseClass {
	protected $class;
	/**
	 *
	 * @param string $class
	 */
	public function __construct($class) {
		if(is_string($class)){
			$this->class = $class;
		}elseif($class instanceof ReflectionClass){
			$this->class = $class->getName();
		}else{
			throw new InvalidArgumentException ( "Invalid type for arg #1" );
		}
	}
	/**
	 * @return string
	 */
	function getClassName() {
		return $this->class;
	}
	/**
	 * @return ReflectionClass
	 */
	function getReflection() {
		return new ReflectionClass ( $this->class );
	}
}