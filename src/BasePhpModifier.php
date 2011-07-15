<?php
namespace goetas\atal;
use InvalidArgumentException;
class BasePhpModifier extends Modifier {
	protected $callback;
	function __construct($callback) {
		if(is_callable($callback)){
			$this->callback = $callback;
		}else{
			throw new InvalidArgumentException(" callback non valida");
		}
	}
	function modify($value, array $params = array()) {
		return  call_user_func_array($this->callback,array_merge(array($value), $params));
	}
}

