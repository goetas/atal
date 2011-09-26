<?php
namespace goetas\atal\plugins\attributes;
use goetas\atal\xml;
use goetas\atal\Attribute;
use goetas\atal\ATal;
use Exception;
class Attribute_attr extends Attribute {
	protected $fatto = false;

	protected $attrsToRemove = array();

	public function prependPI() {
		if(!$this->fatto){
			$this->compiler->getPostFilters()->addFilter( array($this, "_removeAttrs" ) );
			$this->compiler->getPostFilters()->addFilter( array(__CLASS__, "_replaceAttrs" ) );
		}
	}
	public function _removeAttrs($xml) {
		foreach ( $this->attrsToRemove as $k => $attrData ){
			@$attrData [0]->removeAttribute( $attrData [1] );
		}
		return $xml;
	}
	public static function _replaceAttrs($stream) {
		return preg_replace( "/" . preg_quote( 'atal-attr="__atal-attr($', '/' ) . '([A-Za-z0-9_]+)' . preg_quote( ')"', '/' ) . '/', "<?php foreach (\$\\1 as \$__attName => &\$__attValue){" . " echo \$__attName.\"=\\\"\$__attValue\\\" \";" . " } \n unset(\$\\1, \$__attName,\$__attValue); ?>\n", $stream );
	}
	function start(xml\XMLDomElement $node, \DOMAttr $att) {
		$this->prependPI();

		$expressions = $this->compiler->splitExpression( $att->value, ";" );

		$varName = "\$__attr_" . $node->uniqueId();

		$precode =  "if(!isset($varName)){ $varName=array(); }\n";
		$code = '';
		$regex = "/" . preg_quote( "[#tal_attr#", "/" ) . "(" . preg_quote( '$', "/" ) . "[a-zA-Z_\x7f-\xff][a-zA-Z0-9_\x7f-\xff]*)" . preg_quote( "#tal_attr#]", "/" ) . "/";


		foreach ( $expressions as $expression ){
			list ( $condition, $attName, $attExpr ) = $this->splitAttrExpression( $expression );
			if($node->hasAttribute( $attName )){
				$attVal = $node->getAttribute( $attName );

				if(preg_match( $regex, $attVal )){
					$precode = $varName . "['$attName']='" . str_replace(array("___\\'.___","___.\\'___"),array("'.",".'"),addcslashes(  preg_replace( $regex, "___'.___\\1___.'___", $attVal ),"'" )) . "';\n";
				}else{
					$precode .= $varName . "['$attName']='" . addcslashes( $attVal, "'" ) . "';\n";
				}

				$this->attrsToRemove [] = array($node, $attName );
			}

			list ( $prefix, $name ) = explode( ":", $attName );

			if(strlen( $prefix ) && strlen( $name )){
				if($node->lookupNamespaceURI( $prefix ) === null){
					throw new Exception( "Preffisso '$prefix' non ha nessun namespace associato in '{" . $node->namespaceURI . "}" . $node->nodeName . "'" );
				}else{
					$code .= $varName . "['xmlns:$prefix']='" . addcslashes($node->lookupNamespaceURI( $prefix ),"'")  . "'; \n";
				}
			}
			$code .= "if ($condition) { " . $varName . "['$attName']=" . $this->compiler->parsedExpression( $attExpr ) . "; }\n";
		}

		$pi = $this->dom->createProcessingInstruction( "php", $precode . $code );
		if(!$node->parentNode instanceof \DOMElement ){
			throw new Exception("Errore di compilazione del nodo $node->nodeName. ($node->nodeValue)");
		}
		$node->parentNode->insertBefore( $pi, $node );
		$node->setAttribute( "atal-attr", "__atal-attr($varName)" );
	}
	protected function splitAttrExpression($str) {
		$parts = $this->compiler->splitExpression( $str, "?" );
		if(count( $parts ) == 1){
			$attr = $this->findAttrParts( $parts [0] );
			return array('true', $attr ['att'], $attr ['expr'] );
		}elseif(count( $parts ) == 2){
			$attr = $this->findAttrParts( $parts [1] );
			return array($parts [0], $attr ['att'], $attr ['expr'] );
		}else{
			throw new Exception( __CLASS__ . "::splitAttrExpression error in '$str'" );
		}
	}
	public function findAttrParts($str) {
		$mch = array();
		if(preg_match( "/^([a-z_][a-z0-9\\-_]*:[a-z][a-z0-9\\-_]*)\\s*=\\s*/i", $str, $mch )){
			return array('att' => $mch [1], 'expr' => trim( substr( $str, strlen( $mch [0] ) ) ) );
		}elseif(preg_match( "/^([a-z_][a-z0-9\\-_]*)\\s*=\\s*/i", $str, $mch )){
			return array('att' => $mch [1], 'expr' => trim( substr( $str, strlen( $mch [0] ) ) ) );
		}else{
			throw new Exception( __CLASS__ . "::findAttrParts error in '$str'" );
		}
	}

}
