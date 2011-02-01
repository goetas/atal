<?php
namespace goetas\atal\plugins\attributes;
use goetas\atal\xml;
use Exception;
use goetas\atal\Attribute;
class Attribute_call extends Attribute{
	function start(xml\XMLDomElement $node, \DOMAttr $att){
		$pi = $this->dom->createProcessingInstruction("php",self::prepareCode($att, $this->compiler));
		$node->removeChilds();
		$node->appendChild($pi);

	}
	public static  function prepareCode($att, $compiler){

		$expressions=$this->compiler->splitExpression($att->value,";");

		$functname = md5(array_shift($expressions).$compiler->getTemplate());

		$code='';
		foreach ($expressions as $expression){
			$mch=array();
			if(preg_match("/^(".preg_quote( '$', "/" ) . "[a-zA-Z_\\x7f-\\xff][a-zA-Z0-9_\\x7f-\\xff]*)([^=]*)\\s*=\\s*(.+)/",$expression,$mch)){
				$code.="$mch[1]$mch[2] = $mch[3];\n";
			}else{
				throw new Exception("Sintassi plugin non valida: '$att->value'");
			}
		}

		$nome = md5($att->value.$compiler->getTemplate());
		$fcode  = " if (!defined(' __atal_setf_template_{$nome}')) { "; // uso le costanti per oviare ad un bug di php in cui function_exists sbaglia
		$fcode  .= "define(' __atal_setf_template_{$nome}', true);  ";
		$fcode .= " function __atal_setf_template_{$nome} (\$__tal){ \n";
		$fcode .= "   extract(\$__tal->getData());";
		$fcode .= "   {$code};";
		$fcode .= "   \$__tal->addScope(get_defined_vars()); ";
		$fcode .= "   __atal_template_{$functname} (\$__tal ); \n";
		$fcode .= "   \$__tal->removeScope(); ";
		$fcode .= "}\n";
		$fcode .= "}\n";

		$fcode .= " \$__tal->addScope(get_defined_vars()); ";
		$fcode .= "__atal_setf_template_{$nome}(\$__tal); \n";
		$fcode .= " \$__tal->removeScope(); ";


		return $fcode;
	}

}
?>