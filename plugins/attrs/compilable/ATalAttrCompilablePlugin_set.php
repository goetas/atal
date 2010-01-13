<?php
class ATalAttrCompilablePlugin_set extends ATalAttrCompilablePlugin{
	function start(ATal_XMLDomElement $node, $attValue){
		$expressions=ATalCompiler::splitExpression($attValue,";");
		$code='';
		foreach ($expressions as $expression){
			$mch=array();
			if(preg_match("/^(".preg_quote( '$', "/" ) . "[a-zA-Z_\\x7f-\\xff][a-zA-Z0-9_\\x7f-\\xff]*)\\s*=\\s*(.+)/",$expression,$mch)){
				$code.="$mch[1] = $mch[2];\n";
			}else{
				throw new ATalException("Sintassi plugin set non valida");
			}
		}
		$pi = $this->dom->createProcessingInstruction("php",$code);
		$node->parentNode->insertBefore($pi,$node);
	}
}
?>