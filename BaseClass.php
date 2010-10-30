<?php
namespace goetas\atal;
class BaseClass {
	public function __set($varName, $value) {
		throw new \RuntimeException("undefined property $varName");
	}
	public function &__get($varName) {
		throw new \RuntimeException("undefined property $varName");
	}
} 
?>