<?php
require_once dirname(__FILE__).DIRECTORY_SEPARATOR.'ATalAttrCompilablePlugin_attr.php';
class ATalAttrCompilablePlugin_attr_append extends ATalAttrCompilablePlugin_attr {

	function start(ATal_XMLDomElement $node, $attValue) {
		$expressions = ATalCompiler::splitExpression( $attValue, ";" );

		$varName = "\$__attr_" . $node->uniqueId();

		$precode = $varName . " = (array)$varName;\n";
		$code = '';
		$regex = "/" . preg_quote( "[#tal_attr#", "/" ) . "(" . preg_quote( '$', "/" ) . "[a-zA-Z_\x7f-\xff][a-zA-Z0-9_\x7f-\xff]*)" . preg_quote( "#tal_attr#]", "/" ) . "/";
		foreach ( $expressions as $expression ){
			list ( $condition, $attName, $attExpr ) = self::splitAttrExpression( $expression );
			if($node->hasAttribute( $attName )){
				$attVal = $node->getAttribute( $attName );

				if(preg_match( $regex, $attVal )){
					$precode = $varName . "['$attName']=" . preg_replace( $regex, "\\1", $attVal ) . ";\n";
				}else{
					$precode .= $varName . "['$attName']='" . addcslashes( $node->getAttribute( $attName ), "'" ) . "';\n";
				}

				$this->attrs [] = array($node, $attName );
			}

			list ( $prefix, $name ) = explode( ":", $attName );

			if(strlen( $prefix ) && strlen( $name )){
				if($node->lookupNamespaceURI( $prefix ) === NULL){
					throw new ATalException( "Preffisso '$prefix' non ha nessun namespace associato in '{" . $node->namespaceURI . "}" . $node->nodeName . "'" );
				}else{
					$code .= $varName . "['xmlns:$prefix']='" . addcslashes($node->lookupNamespaceURI( $prefix ),"'")  . "'; \n";
				}
			}
			$code .= "if ($condition) { " . $varName . "['$attName'].=" . $this->compiler->parsedExpression( $attExpr ) . "; }\n";
		}
		$pi = $this->dom->createProcessingInstruction( "php", $precode . $code );
		$node->parentNode->insertBefore( $pi, $node );
		$node->setAttribute( "atal-attr", "__atal-attr($varName)" );
	}

}
