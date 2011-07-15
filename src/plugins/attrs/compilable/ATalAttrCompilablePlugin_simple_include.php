<?php
class ATalAttrCompilablePlugin_simple_include extends ATalAttrCompilablePlugin{
	function start(ATal_XMLDomElement $node, $attValue){
		if($attValue[0]=="#"){
			$attValue = $this->compiler->getTemplate().$attValue;
		}
		$pi = $this->dom->createProcessingInstruction("php",

		"\$__tal_odir = getcwd();".
		" chdir(dirname(\$__tal->getTemplate())); ".
		" echo @file_get_contents(\"$attValue\");".
		" chdir(\$__tal_odir); \n"
		);
		$node->removeChilds();
		$node->appendChild($pi);
	}


}
?>