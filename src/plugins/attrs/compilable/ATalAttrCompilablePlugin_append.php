<?php
class ATalAttrCompilablePlugin_append extends ATalAttrCompilablePlugin{
	function start(ATal_XMLDomElement  $node, $attValue){
		$pi = $this->dom->createProcessingInstruction("php","print( ".$this->compiler->parsedExpression($attValue)." ) ; ");
		$node->appendChild($pi);
	}


}
?>