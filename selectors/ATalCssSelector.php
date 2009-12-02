<?php
require_once dirname(__FILE__).DIRECTORY_SEPARATOR.'QueryPath'.DIRECTORY_SEPARATOR.'QueryPath.php';
class ATalCssSelector extends ATalSelector {
	public function select($query){
		return qp($this->dom,$query)->get();
	}
}

