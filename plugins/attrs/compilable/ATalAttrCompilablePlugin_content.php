<?php
class ATalAttrCompilablePlugin_content extends ATalAttrCompilablePlugin{
	function start(XMLDomElement $node, $attValue){
		if(strlen(trim($attValue))>0){
			$attValue = $this->compiler->parsedExpression( $attValue );
			$pi = $this->dom->createProcessingInstruction("php"," print( $attValue );");
			$node->removeChilds();
			$node->appendChild($pi);
		}else{
			$node->removeChilds();
		}
	}


}
?>