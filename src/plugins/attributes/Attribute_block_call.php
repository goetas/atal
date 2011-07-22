<?php
namespace goetas\atal\plugins\attributes;
use goetas\atal\xml;
use Exception;
use goetas\atal\Attribute;
use goetas\atal\Compiler;
class Attribute_block_call extends Attribute{
	function start(xml\XMLDomElement $node, \DOMAttr $att){
		$pi = $this->dom->createProcessingInstruction("php",self::prepareCode($att, $this->compiler));
		$node->parentNode->replaceChild($pi, $node);
		return self::STOP_NODE | self::STOP_ATTRIBUTE;
	}
	public static function prepareCode(\DOMAttr $att, Compiler $compiler){

		$expressions = $compiler->splitExpression($att->value,";");

		$functname = md5(array_shift($expressions).$compiler->getTemplate());

		$code='';
		foreach ($expressions as $expression){
			$mch=array();
			if(preg_match("/^(".preg_quote( '$', "/" ) . "[a-zA-Z_\\x7f-\\xff][a-zA-Z0-9_\\x7f-\\xff]*)([^=]*)\\s*=\\s*(.+)/", $expression, $mch)){
				$code.="$mch[1]$mch[2] = $mch[3];\n";
			}else{
				throw new Exception("Sintassi plugin non valida: '$att->value'");
			}
		}

		$fcode = "\$this->{$att->value}(); ";
		return $fcode;
	}

}
