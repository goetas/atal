<?php
namespace goetas\atal\plugins\attributes;
use goetas\atal\xml;
use goetas\atal\Attribute;
class Attribute_include extends Attribute{
	function start(xml\XMLDomElement $node, \DOMAttr $att){
		if($att->value[0]=="#"){
			$att->value = $this->compiler->getTemplate().$att->value;
		}
		$pi = $this->dom->createProcessingInstruction("php",$this->generatePI($node,$att));
		$node->removeChilds();
		$node->appendChild($pi);
		return self::STOP_NODE;
	}

	protected function generatePI(xml\XMLDomElement $node, \DOMAttr $att) {
		$piStr = " try{
			 \$__ntal = clone(\$this->getTal());\n".

			" \$__ntal->addScope(get_defined_vars());\n".
			" \$__ntal->xmlDeclaration = false;\n \$__ntal->dtdDeclaration = false;\n".

			" echo \$__ntal->get(".var_export($this->atal->getFinder()->getRelativeTo($att->value, $this->compiler->getTemplate()),1).");\n".
		"}catch(\\Exception \$__tal_exception){".
			"echo htmlspecialchars(\$__tal_exception->getMessage(),ENT_QUOTES,'utf-8');".
		"}\n".
		"unset(\$__ntal,\$__tal_exception);\n";
		return $piStr;
	}
}
