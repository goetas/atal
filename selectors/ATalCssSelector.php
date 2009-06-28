<?php
require_once dirname(__FILE__).'QueryPath/QueryPath.php';
class ATalCssSelector extends ATalSelector {
	public function select($query){
		return qp($this->dom,$query);
	}
}

