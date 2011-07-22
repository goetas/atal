<?php
namespace goetas\atal\plugins\selectors;
use goetas\atal\Selector;
class Selector_xpath extends Selector {
	public function select($query){
		$parts = $this->compiler->splitExpression( $query, ';' );
		$ns = array();
		for($i = 1; $i < count( $parts ); $i ++){
			list ( $prefix, $uri ) = $this->compiler->splitExpression( $parts [$i], '=' );
			$ns [$prefix] = trim( $uri, "'\t\n\r\"" );
		}
		return $this->dom->query( $parts [0], $ns );
	}
}

