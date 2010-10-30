<?php
namespace goetas\atal\plugins\attributes\compilable;
use goetas\atal\xml;
use goetas\atal\CompilableAttribute;
class CompilableAttribute_if extends CompilableAttribute {
	static $cnt = 0;
	public function start(xml\XMLDomElement $node, \DOMAttr $att) {
		self::$cnt ++;
		$piS = $this->dom->createProcessingInstruction( "php", "if( $att->value ) :  // if " . self::$cnt . " " );
		$piE = $this->dom->createProcessingInstruction( "php", " endif; // if " . self::$cnt . " " );
		
		$node->parentNode->insertBefore( $piS, $node );
		$node->parentNode->insertAfter( $piE, $node );
	
	}
}
?>