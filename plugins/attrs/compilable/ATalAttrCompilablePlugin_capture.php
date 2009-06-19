<?php
class ATalAttrCompilablePlugin_capture extends ATalAttrCompilablePlugin{
	function start(XMLDomElement $node, $attValue){
		
		$piS = $this->dom->createProcessingInstruction("php"," ob_start(); ");
		$piE = $this->dom->createProcessingInstruction("php"," \$$attValue = ob_get_clean() ; ");
		
		$node->parentNode->insertBefore($piS, $node);	
		$node->parentNode->insertAfter($piE, $node);	

	}

}
?>