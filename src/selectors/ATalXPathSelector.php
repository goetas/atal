<?php

class ATalXPathSelector extends ATalSelector {
	public function select($query){
		$parts = ATalCompiler::splitExpression( $query, ';' );
		$ns = array();
		for($i = 1; $i < count( $parts ); $i ++){
			list ( $prefix, $uri ) = ATalCompiler::splitExpression( $parts [$i], '=' );
			$ns [$prefix] = trim( $uri, "'\t\n\r\"" );
		}
		
		return $this->dom->query( $parts [0], $ns );
	}
}

