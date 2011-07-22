<?php
namespace goetas\atal\plugins\attributes;
use goetas\atal\xml;
use goetas\atal\Attribute;
class Attribute_simple_include extends Attribute{
	function start(xml\XMLDomElement $node, \DOMAttr $att){
		if($att->value[0]=="#"){
			$att->value = $this->compiler->getTemplate().$att->value;
		}
		$pi = $this->dom->createProcessingInstruction("php",

		"\$__tal_odir = getcwd();".
		" chdir(".var_export(dirname($this->compiler->getTemplate()),1)."); ".
		" echo @file_get_contents(\"$att->value\");".
		" chdir(\$__tal_odir);unset(\$__tal_odir); \n"
		);
		$node->removeChilds();
		$node->appendChild($pi);
	}


}
?>