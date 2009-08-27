<?php
class ATalAttrCompilablePlugin_template extends ATalAttrCompilablePlugin{
	function start(ATal_XMLDomElement $node, $attValue){
		$nome = $attValue.md5($this->compiler->getTemplate());
		$piStart = $this->dom->createProcessingInstruction("php",
		" if (!function_exists('__atal_template_".$nome."')){ function __atal_template_".$nome." (\$__tal ){ ".
		" extract(\$__tal->getData()); ");
		$node->parentNode->insertBefore($piStart, $node);

		$piEnd = $this->dom->createProcessingInstruction("php"," }} ");
		$node->parentNode->insertAfter($piEnd, $node);

		if($node->hasAttributeNS(ATal::NS, 'call') && $node->getAttributeNS(ATal::NS,"call") == $attValue){
			$pi = $this->dom->createProcessingInstruction("php",ATalAttrCompilablePlugin_call::prepareCode($attValue, $this->compiler));		
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