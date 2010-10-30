<?php
/**
 * Definisce delle variabili per l ATal
 * ES:
 * &lt;img xmlns:t="ATal" t:translate-var="$var='Estivi'" title="eventi %tipo" t:translate-attr="title(tipo=$var)"/&gt;
 * &lt;p xmlns:t="ATal" t:translate-var="$var='Eventi del %anno'" t:content="$var|translate-var:anno='2009'"&gt;testo di prova&lt;/p&gt;
 */
namespace goetas\atal\plugins\attributes\compilable;
use goetas\atal\xml;
use goetas\atal\CompilableAttribute;
class CompilableAttribute_translate_var extends CompilableAttribute {
	function start(xml\XMLDomElement $node, \DOMAttr $att) {
		foreach ( $this->compiler->splitExpression( $att->value, ";" ) as $part ){
			list ( $varName, $expr ) = $this->compiler->splitExpression( $part, "=" );
			$first = $expr[0];
			$last = $expr[strlen($expr)-1];
			if(! (($first=="\""  &&  $last== "\"" ) || ( $first== "'"  &&  $last== "'" ))){
				throw new Exception( "errore di sintassi vicino a '" . $expr . "'" );
			}
			if($varName[0]!='$'){
				throw new Exception( "errore di sintassi vicino a '" . $varName . "'" );
			}
			$code .= "$varName = " . $expr . ";\n";
		}

		$pi = $this->dom->createProcessingInstruction( "php", $code );
		$node->parentNode->insertBefore( $pi, $node );
	}
}