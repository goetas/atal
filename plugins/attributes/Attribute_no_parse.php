<?php
namespace goetas\atal\plugins\attributes;
use goetas\atal\xml;
use goetas\atal\ATal;
use goetas\atal\Attribute;
class Attribute_no_parse extends Attribute {
	function start(xml\XMLDomElement $node, \DOMAttr $att) {
		foreach ( $node->query( ".//*[@t:id]/@t:id", array("t" => ATal::NS ) ) as $tt ){
			$tt->ownerElement->removeAttributeNode( $tt );
		}
		return self::STOP_NODE;
	}

}