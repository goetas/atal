<?php
class ATalAttrCompilablePlugin_call extends ATalAttrCompilablePlugin{
	function start(ATal_XMLDomElement $node, $attValue){
		$pi = $this->dom->createProcessingInstruction("php",self::prepareCode($attValue, $this->compiler));
		$node->removeChilds();
		$node->appendChild($pi);

	}
	public static  function prepareCode($attValue, $compiler){
		$nome = $attValue.md5($compiler->getTemplate());
		return " \$__tal->addScope(get_defined_vars()); __atal_template_{$nome} (\$__tal ); \$__tal->removeScope();";
	}

}
?>