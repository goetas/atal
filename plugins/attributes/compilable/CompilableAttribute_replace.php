<?php
namespace goetas\atal\plugins\attributes\compilable;
use goetas\atal\xml;
use goetas\atal\CompilableAttribute;
class CompilableAttribute_replace extends CompilableAttribute{
	function start(xml\XMLDomElement $node, \DOMAttr $att){
		if(strlen(trim($att->value))>0){
			$pi = $this->dom->createProcessingInstruction("php","print( ".$this->compiler->parsedExpression($att->value)." ) ; ");
			$node->parentNode->replaceChild($pi, $node);
		}else{
			$node->remove();
		}
		return self::STOP_NODE | self::STOP_ATTRIBUTE;
	}


}
?>