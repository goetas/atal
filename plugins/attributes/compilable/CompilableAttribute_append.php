<?php
namespace goetas\atal\plugins\attributes\compilable;
use goetas\atal\xml;
use goetas\atal\CompilableAttribute;
class CompilableAttribute_append extends CompilableAttribute{
	function start(xml\XMLDomElement  $node, \DOMAttr $att){
		$pi = $this->dom->createProcessingInstruction("php","print( ".$this->compiler->parsedExpression($att->value)." ) ; ");
		$node->appendChild($pi);
	}


}
?>