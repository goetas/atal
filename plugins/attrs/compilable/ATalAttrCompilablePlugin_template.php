<?php
class ATalAttrCompilablePlugin_template extends ATalAttrCompilablePlugin{
	function start(XMLDomElement $node, $attValue){
		$piStart = $this->dom->createProcessingInstruction("php",
		" function __atal_template_".$attValue." (\$__tal ){ ".
		" extract(\$__tal->getData()); ");
		$node->parentNode->insertBefore($piStart, $node);

		$piEnd = $this->dom->createProcessingInstruction("php"," } ");
		$node->parentNode->insertAfter($piEnd, $node);

		if($node->hasAttributeNS(ATal::NS, 'call') && $node->getAttributeNS(ATal::NS,"call") == $attValue){
			$pi = $this->dom->createProcessingInstruction("php",ATalAttrCompilablePlugin_call::prepareCode($attValue));		
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