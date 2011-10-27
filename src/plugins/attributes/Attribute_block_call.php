<?php
namespace goetas\atal\plugins\attributes;
use goetas\xml;
use Exception;
use goetas\atal\Attribute;
use goetas\atal\Compiler;
class Attribute_block_call extends Attribute{
	function start(xml\XMLDomElement $node, \DOMAttr $att){
		$pi = $this->dom->createProcessingInstruction("php",self::prepareCode($att, $this->compiler));
		$node->removeChilds();
		$node->appendChild($pi);
		return self::STOP_NODE | self::STOP_ATTRIBUTE;
	}
	public static function prepareCode(\DOMAttr $att, Compiler $compiler){

		$expressions = $compiler->splitExpression($att->value,";");

		$functname = array_shift($expressions);

		if(count($expressions)){
			$code="call_user_func(function(\$data){\n\t";
			$code.="extract(\$data);";
			foreach ($expressions as $expression){
				$mch=array();
				if(preg_match("/^(".preg_quote( '$', "/" ) . "[a-zA-Z_\\x7f-\\xff][a-zA-Z0-9_\\x7f-\\xff]*)([^=]*)\\s*=\\s*(.+)/", $expression, $mch)){
					$code.="$mch[1]$mch[2] = $mch[3];\n";
				}else{
					throw new Exception("Sintassi plugin non valida: '$att->value'");
				}
			}
			$code .=";\n";
			$code .="\$ret = get_defined_vars(); unset(\$ret['__atal__scope']);\n";
			$code .="return \$ret;\n}, get_defined_vars())\n";
		}else{
			$code="get_defined_vars()";
		}

		//$fcode = "\$this->addScope(get_defined_vars()); ";

		$fcode .= "\$this->{$functname}($code); ";
		//$fcode .= "\$this->removeScope(); ";

		return $fcode;
	}

}
