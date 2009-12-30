<?php
class ATalAttrCompilablePlugin_replace extends ATalAttrCompilablePlugin{
	function start(ATal_XMLDomElement $node, $attValue){
		if(strlen(trim($attValue))>0){
			$pi = $this->dom->createProcessingInstruction("php","print( ".$this->compiler->parsedExpression($attValue)." ) ; ");
			$node->parentNode->replaceChild($pi, $node);
		}else{
			$node->remove();
		}
		return self::STOP_NODE | self::STOP_ATTRIBUTE;
	}


}
?>