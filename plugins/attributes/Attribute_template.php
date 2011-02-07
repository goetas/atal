<?php
namespace goetas\atal\plugins\attributes;
use goetas\atal\xml;
use goetas\atal\ATal;
use DOMException;
use goetas\atal\Attribute;
class Attribute_template extends Attribute{
	function start(xml\XMLDomElement $node, \DOMAttr $att){
		$nome = md5($att->value.$this->compiler->getTemplate());
		$piStart = $this->dom->createProcessingInstruction("php",
		" if (!function_exists('__atal_template_".$nome."')){ function __atal_template_".$nome." (\$__tal ){ ".
		" extract(\$__tal->getData()); ");
		$node->parentNode->insertBefore($piStart, $node);

		$piEnd = $this->dom->createProcessingInstruction("php"," }} ");
		$node->parentNode->insertAfter($piEnd, $node);

		if($node->hasAttributeNS(ATal::NS, 'call') && $node->getAttributeNS(ATal::NS,"call") == $att->value){
			$pi = $this->dom->createProcessingInstruction("php",Attribute_call::prepareCode($att, $this->compiler));
			$node->parentNode->insertAfter($pi, $piEnd);
			try {
				$node->removeAttributeNS(ATal::NS,"call");
			}catch (DOMException $e){

			}
		}
	}
	function depends(){
		return array("call");
	}
}
?>
