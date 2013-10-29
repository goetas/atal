<?php
namespace goetas\atal\plugins\attributes;
use goetas\xml;
use goetas\atal\Attribute;
class Attribute_content extends Attribute{
	function start(xml\XMLDomElement $node, \DOMAttr $att){
		if(strlen(trim($att->value))>0){
			$pi = $this->dom->createTextnode("{{ $att->value }}");
			$node->removeChilds();
			$node->appendChild($pi);
		}else{
			$node->removeChilds();
		}
		return self::STOP_NODE;
	}
}
