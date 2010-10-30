<?php
namespace goetas\atal\plugins\attributes\compilable;
use goetas\atal\xml;
use goetas\atal\CompilableAttribute;
class CompilableAttribute_content extends CompilableAttribute{
	function start(xml\XMLDomElement $node, \DOMAttr $att){
		if(strlen(trim($att->value))>0){
			$att->value = $this->compiler->parsedExpression( $att->value );
			$pi = $this->dom->createProcessingInstruction("php"," print( $att->value );");
			$node->removeChilds();
			$node->appendChild($pi);
		}else{
			$node->removeChilds();
		}
		return self::STOP_NODE;
	}


}
?>