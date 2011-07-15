<?php
class ATalAttrCompilablePlugin_call extends ATalAttrCompilablePlugin{
	function start(ATal_XMLDomElement $node, $attValue){
		$pi = $this->dom->createProcessingInstruction("php",self::prepareCode($attValue, $this->compiler));
		$node->removeChilds();
		$node->appendChild($pi);

	}
	public static  function prepareCode($attValue, $compiler){

		$expressions=ATalCompiler::splitExpression($attValue,";");

		$functname = md5(array_shift($expressions).$compiler->getTemplate());

		$code='';
		foreach ($expressions as $expression){
			$mch=array();
			if(preg_match("/^(".preg_quote( '$', "/" ) . "[a-zA-Z_\\x7f-\\xff][a-zA-Z0-9_\\x7f-\\xff]*)([^=]*)\\s*=\\s*(.+)/",$expression,$mch)){
				$code.="$mch[1]$mch[2] = $mch[3];\n";
			}else{
				throw new ATalException("Sintassi plugin non valida: '$attValue'");
			}
		}

		$nome = md5($attValue.$compiler->getTemplate());
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