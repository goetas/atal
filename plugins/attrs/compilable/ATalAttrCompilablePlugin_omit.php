<?php
class ATalAttrCompilablePlugin_omit extends ATalAttrCompilablePlugin {
	static $cnt = 0;
	function start(ATal_XMLDomElement $node, $attValue) {
		
		$piIf = $this->dom->createProcessingInstruction( "php", "if( !( $attValue ) ){ // omit " . self::$cnt . " " );
		$piElse = $this->dom->createProcessingInstruction( "php", "} else {  // omit " . self::$cnt . " " );
		$piEndIf = $this->dom->createProcessingInstruction( "php", "}  // omit " . self::$cnt . " " );
		self::$cnt ++;
		
		$node->parentNode->insertBefore( $piIf, $node );
		
		$node->parentNode->insertAfter( $piElse, $node );
		
		$node->parentNode->insertAfter( $piEndIf, $piElse );
		
		$nodes = array();
		foreach ( $node->childNodes as $subNode ){
			$nodes [] = $newRef = $subNode->cloneNode( true );
			$node->parentNode->insertBefore( $newRef, $piEndIf );
		}
		foreach ( $nodes as $newNode ){
			if($newNode instanceof XMLDomElement){
				$this->compiler->applyTemplats( $newNode );
			}elseif($newNode instanceof DOMText){
				$this->compiler->applyTextVars( $newNode );
			}
		}
	}
}
?>