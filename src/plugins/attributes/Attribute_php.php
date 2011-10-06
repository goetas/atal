<?php
namespace goetas\atal\plugins\attributes;
use goetas\xml;
use goetas\atal\Attribute;
class Attribute_php extends Attribute{
	function start(xml\XMLDomElement $node, \DOMAttr $att){

		$pi = $this->dom->createProcessingInstruction("php", "\n".htmlspecialchars_decode($node->saveXML(false)));
		$node->parentNode->replaceChild($pi, $node);

		return self::STOP_NODE | self::STOP_ATTRIBUTE;
	}
}
