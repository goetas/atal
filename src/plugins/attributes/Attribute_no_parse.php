<?php
namespace goetas\atal\plugins\attributes;
use goetas\xml;
use goetas\atal\ATal;
use goetas\atal\Attribute;
class Attribute_no_parse extends Attribute {
	function start(xml\XMLDomElement $node, \DOMAttr $att) {
		return self::STOP_NODE;
	}

}