<?php
class ATalAttrCompilablePlugin_attr extends ATalAttrCompilablePlugin {
	protected $attrs = array();
	public function init() {
		$this->compiler->addPostFilter( array(__CLASS__, "replaceAttrs" ) );
		$this->compiler->addPreWriteFilter( array($this, "removeAttrs" ) );
	}
	public function removeAttrs() {
		foreach ( $this->attrs as $k => $attrData ){
			@$attrData [0]->removeAttribute( $attrData [1] );
		}
	}
	public static function replaceAttrs($stream) {
		return preg_replace( "/" . preg_quote( 'atal-attr="__atal-attr($', '/' ) . '([A-Za-z0-9_]+)' . preg_quote( ')"', '/' ) . '/', "<?php foreach (\$\\1 as \$__attName => \$__attValue){" . " echo \$__attName.\"=\\\"\$__attValue\\\" \";" . " } \n unset(\$\\1, \$__attName,\$__attValue); ?>", $stream );
	}
	function start(XMLDomElement $node, $attValue) {
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
			$code .= "if ($condition) { " . $varName . "['$attName']=" . $this->compiler->parsedExpression( $attExpr ) . "; }\n";
		}
		$pi = $this->dom->createProcessingInstruction( "php", $precode . $code );
		$node->parentNode->insertBefore( $pi, $node );
		$node->setAttribute( "atal-attr", "__atal-attr($varName)" );
	}
	protected static function splitAttrExpression($str) {
		$parts = ATalCompiler::splitExpression( $str, "?" );
		if(count( $parts ) == 1){
			$attr = self::findAttrParts( $parts [0] );
			return array('true', $attr ['att'], $attr ['expr'] );
		}elseif(count( $parts ) == 2){
			$attr = self::findAttrParts( $parts [1] );
			return array($parts [0], $attr ['att'], $attr ['expr'] );
		}else{
			throw new ATalException( __CLASS__ . "::splitAttrExpression error in '$str'" );
		}
	}
	public static function findAttrParts($str) {
		$mch = array();
		if(preg_match( "/^([a-z_][a-z0-9\-_]*:[a-z][a-z0-9\-_]*)\s*=\s*/i", $str, $mch )){
			return array('att' => $mch [1], 'expr' => trim( substr( $str, strlen( $mch [0] ) ) ) );
		}elseif(preg_match( "/^([a-z_][a-z0-9\-_]*)\s*=\s*/i", $str, $mch )){
			return array('att' => $mch [1], 'expr' => trim( substr( $str, strlen( $mch [0] ) ) ) );
		}else{
			throw new ATalException( __CLASS__ . "::findAttrParts error in '$str'" );
		}
	}

}
?>
