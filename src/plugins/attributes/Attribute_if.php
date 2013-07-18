<?php
namespace goetas\atal\plugins\attributes;
use goetas\xml;
use goetas\atal\Attribute;
class Attribute_if extends Attribute {
	static $cnt = 0;
	public function start(xml\XMLDomElement $node, \DOMAttr $att) {
		self::$cnt ++;
		$piS = $this->dom->createProcessingInstruction( "php", "if( $att->value ) {  // if " . self::$cnt . " " );
		$piE = $this->dom->createProcessingInstruction( "php", " } // if " . self::$cnt . " " );

		$node->parentNode->insertBefore( $piS, $node );
		$node->parentNode->insertAfter( $piE, $node );

	}
}
