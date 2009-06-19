<?php
class ATalAttrCompilablePlugin_call extends ATalAttrCompilablePlugin{
	function start(XMLDomElement $node, $attValue){
		$pi = $this->dom->createProcessingInstruction("php",self::prepareCode($attValue));		
		$node->removeChilds();
		$node->appendChild($pi);

	}
	public static  function prepareCode($attValue){
		return " \$__tal->addScope(get_defined_vars()); __atal_template_{$attValue} (\$__tal ); \$__tal->removeScope();";
	}

}
?>