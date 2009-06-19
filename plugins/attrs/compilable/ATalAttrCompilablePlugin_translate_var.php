<?php
/**
 * Definisce delle variabili per l ATal
 * ES:
 * &lt;img xmlns:t="ATal" t:translate-var="$var='Estivi'" title="eventi %tipo" t:translate-attr="title(tipo=$var)"/&gt;
 * &lt;p xmlns:t="ATal" t:translate-var="$var='Eventi del %anno'" t:content="$var|translate-var:anno='2009'"&gt;testo di prova&lt;/p&gt;
 */

class ATalAttrCompilablePlugin_translate_var extends ATalAttrCompilablePlugin {
	function start(XMLDomElement $node, $attValue) {
		$code = '';
		foreach ( ATalCompiler::splitExpression( $attValue, ";" ) as $part ){
			list ( $varName, $expr ) = ATalCompiler::splitExpression( $part, "=" );
			if(! ((starts_with( $expr, "\"" ) && ends_with( $expr, "\"" )) || (starts_with( $expr, "'" ) && ends_with( $expr, "'" )))){
				throw new ATalException( "errore di sintassi vicino a '" . $expr . "'" );
			}
			if(! starts_with($varName,'$') ){
				throw new ATalException( "errore di sintassi vicino a '" . $varName . "'" );
			}
			$code .= "$varName = " . $expr . ";\n";
		}
		
		$pi = $this->dom->createProcessingInstruction( "php", $code );
		$node->parentNode->insertBefore( $pi, $node );
	}
}