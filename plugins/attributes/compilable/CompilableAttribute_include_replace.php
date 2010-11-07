<?php
namespace goetas\atal\plugins\attributes\compilable;
use goetas\atal\xml;
use goetas\atal\CompilableAttribute;
class CompilableAttribute_include_replace extends CompilableAttribute{
	function start(xml\XMLDomElement $node, \DOMAttr $att){
		if($att->value[0]=="#"){
			$att->value = $this->compiler->getTemplate().$att->value;
		}
		$pi = $this->dom->createProcessingInstruction("php",
		" try{
		 \$__ntal = clone(\$__tal); ".
		" \$__tal_odir = getcwd();".
		" chdir(".var_export(dirname($this->compiler->getTemplate()),1)."); ".

		" \$__ntal->addScope(get_defined_vars());".
		" \$__ntal->xmlDeclaration = false;".

		" echo \$__ntal->get(\"$att->value\"); unset(\$__ntal);".
		" chdir(\$__tal_odir); \n".
		"}catch(\\Exception \$__tal_exception){".
		"chdir(\$__tal_odir);".
		"echo htmlspecialchars(\$__tal_exception->getMessage(),ENT_QUOTES,'utf-8');".
		"}\n".
		"unset(\$__tal_odir);\n"
		
		);
		$node->parentNode->replaceChild($pi, $node);
		return self::STOP_NODE | self::STOP_ATTRIBUTE;
	}


}
?>