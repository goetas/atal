<?php
class ATalAttrCompilablePlugin_replace_cdata extends ATalAttrCompilablePlugin{
	function start(ATal_XMLDomElement $node, $attValue){
		
		$pi = $this->dom->createProcessingInstruction("php","print('<![CDATA[' . ".$this->compiler->parsedExpression($attValue)." .']]>' ) ; ");
		$node->parentNode->replaceChild($pi, $node);	

		return false;	
	}


}
?>