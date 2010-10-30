<?php
namespace goetas\atal\plugins\attributes\compilable;
use goetas\atal\xml;
use goetas\atal\CompilableAttribute;
class CompilableAttribute_template extends CompilableAttribute{
	function start(xml\XMLDomElement $node, \DOMAttr $att){
		$nome = md5($att->value.$this->compiler->getTemplate());
		$piStart = $this->dom->createProcessingInstruction("php",
		" if (!function_exists('__atal_template_".$nome."')){ function __atal_template_".$nome." (\$__tal ){ ".
		" extract(\$__tal->getData()); ");
		$node->parentNode->insertBefore($piStart, $node);

		$piEnd = $this->dom->createProcessingInstruction("php"," }} ");
		$node->parentNode->insertAfter($piEnd, $node);

		if($node->hasAttributeNS(ATal::NS, 'call') && $node->getAttributeNS(ATal::NS,"call") == $att->value){
			$pi = $this->dom->createProcessingInstruction("php",CompilableAttribute_call::prepareCode($att->value, $this->compiler));
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