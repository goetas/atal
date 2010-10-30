<?php
namespace goetas\atal\plugins\attributes\compilable;
use goetas\atal\xml;
use goetas\atal\CompilableAttribute;
class CompilableAttribute_replace_cdata extends CompilableAttribute{
	function start(xml\XMLDomElement $node, \DOMAttr $att){

		$pi = $this->dom->createProcessingInstruction("php","print('<![CDATA[' . ".$this->compiler->parsedExpression($att->value)." .']]>' ) ; ");
		$node->parentNode->replaceChild($pi, $node);

		return self::STOP_NODE;
	}


}
?>