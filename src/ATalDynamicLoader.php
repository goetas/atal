<?php
class ATalDynamicLoader{
	protected $element;
	protected $path='*';
	function __construct($element, $path='*'){
		$this->element = $element;
		if ($path!=='*' && is_readable($path)){
			$this->path = realpath($path);
		}elseif($path!=='*'){
			throw new ATalException(get_class().": non trovo il file '$path'");
		}
	}
	function loadFile(){
		if($this->path!=='*'){
			include_once( $this->path );
		}
	}
	function getName(){
		return $this->element;
	}
}
