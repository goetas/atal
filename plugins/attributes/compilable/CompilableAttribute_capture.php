<?php
namespace goetas\atal\plugins\attributes\compilable;
use goetas\atal\xml;
use goetas\atal\CompilableAttribute;
class CompilableAttribute_capture extends CompilableAttribute{
	function start(xml\XMLDomElement $node, \DOMAttr $att){
		
		$piS = $this->dom->createProcessingInstruction("php"," ob_start(); ");
		$piE = $this->dom->createProcessingInstruction("php"," \$$att->value = ob_get_clean() ; ");
		
		$node->parentNode->insertBefore($piS, $node);	
		$node->parentNode->insertAfter($piE, $node);	

	}

}
?>