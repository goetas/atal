<?php
class ATalAttrCompilablePlugin_include extends ATalAttrCompilablePlugin{
	function start(XMLDomElement $node, $attValue){
		if($attValue[0]=="#"){
			$attValue = $this->compiler->getTemplate().$attValue;	
		}
		$pi = $this->dom->createProcessingInstruction("php",
		" try{
		 \$__ntal = clone(\$__tal); ".
		" \$__tal_odir = getcwd();".
		" chdir(dirname(\$__tal->getTemplate())); ".
		
		" \$__ntal->addScope(get_defined_vars());".
		" \$__ntal->xmlDeclaration = false; \$__ntal->dtdDeclaration = false;".
		
		" echo \$__ntal->get(\"$attValue\"); unset(\$__ntal);".
		" chdir(\$__tal_odir); \n".
		"}catch(Exception \$__tal_exception){".
		"chdir(\$__tal_odir);". 
		"echo htmlspecialchars(\$__tal_exception->getMessage(),ENT_QUOTES,'utf-8');".
		"}"
		);
		$node->removeChilds();
		$node->appendChild($pi);		
	}


}
?>