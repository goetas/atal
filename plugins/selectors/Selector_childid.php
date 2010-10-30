<?php
namespace goetas\atal\plugins\selectors;
use goetas\atal\Selector;
class Selector_childid extends Selector {
	public function select($query){
		return $this->dom->query( "//*[@id = '$query' ]/node()" );
	}
}

