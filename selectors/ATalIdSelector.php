<?php

class ATalIdSelector extends ATalSelector {
	public function select($query){
		return $this->dom->query( "//*[@id = '$query' ]" );
	}
}

