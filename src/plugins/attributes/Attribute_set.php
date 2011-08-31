<?php
namespace goetas\atal\plugins\attributes;
use goetas\atal\xml;
use Exception;
use goetas\atal\Attribute;
class Attribute_set extends Attribute{
	function start(xml\XMLDomElement $node, \DOMAttr $att){
		$expressions=$this->compiler->splitExpression($att->value,";");
		$code='';
		foreach ($expressions as $expression){
			$mch=array();
			if(preg_match("/^(".preg_quote( '$', "/" ) . "[a-zA-Z_\\x7f-\\xff][a-zA-Z0-9_\\x7f-\\xff]*)([^=]*)\\s*=\\s*(.+)/", $expression, $mch)){
				$code.="$mch[1]$mch[2] = $mch[3];\n";
			}else{
				throw new Exception("Sintassi plugin set non valida: '$att->value'");
			}
		}
		$pi = $this->dom->createProcessingInstruction("php", $code);
		$node->parentNode->insertBefore($pi, $node);
	}
}
