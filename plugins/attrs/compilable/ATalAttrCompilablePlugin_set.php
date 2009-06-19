<?php
class ATalAttrCompilablePlugin_set extends ATalAttrCompilablePlugin{
	function start(XMLDomElement $node, $attValue){
		$expressions=ATalCompiler::splitExpression($attValue,";");
		$code='';
		foreach ($expressions as $expression){
			list ($varName, $expr)=ATalCompiler::splitExpression($expression,"=");
			$code.="$varName = ".$expr.";\n";			
		}
			
		$pi = $this->dom->createProcessingInstruction("php",$code);
		$node->parentNode->insertBefore($pi,$node);	
	}
}
?>