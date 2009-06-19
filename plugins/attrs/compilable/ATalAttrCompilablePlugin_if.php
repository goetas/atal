<?php
class ATalAttrCompilablePlugin_if extends ATalAttrCompilablePlugin {
	static $cnt = 0;
	function start(XMLDomElement $node, $attValue) {
		self::$cnt ++;
		$piS = $this->dom->createProcessingInstruction( "php", "if( $attValue ) :  // if " . self::$cnt . " " );
		$piE = $this->dom->createProcessingInstruction( "php", " endif; // if " . self::$cnt . " " );
		
		$node->parentNode->insertBefore( $piS, $node );
		$node->parentNode->insertAfter( $piE, $node );
	
	}
}
?>