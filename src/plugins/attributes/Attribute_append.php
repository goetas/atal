<?php
namespace goetas\atal\plugins\attributes;
use goetas\xml;
use goetas\atal\Attribute;
class Attribute_append extends Attribute{
	function start(xml\XMLDomElement  $node, \DOMAttr $att){
		$pi = $this->dom->createProcessingInstruction("php","print( ".$this->compiler->parsedExpression($att->value)." ) ; ");
		$node->appendChild($pi);
	}


}