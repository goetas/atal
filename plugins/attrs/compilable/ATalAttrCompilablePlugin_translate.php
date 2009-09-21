<?php
class ATalAttrCompilablePlugin_translate extends ATalAttrCompilablePlugin {
	public function init() {
		$pi = $this->dom->createProcessingInstruction( "php", " require_once( '" . addslashes( __FILE__ ) . "'); " );
		$this->dom->insertBefore( $pi, $this->dom->documentElement );
	}
	function start(ATal_XMLDomElement $node, $attValue) {
		$parts = ATalCompiler::splitExpression( $attValue, ";" );
		$params = array();
		foreach ( $parts as $part ){
			list ( $k, $v ) = ATalCompiler::splitExpression( $part, "=" );
			$params [$k] = $this->compiler->parsedExpression( $v );
		}
		foreach ( $node->query( ".//*[@t:id]/@t:id", array("t" => ATal::NS ) ) as $tt ){
			$tt->ownerElement->removeAttributeNode( $tt );
		}
		$pi = $this->dom->createProcessingInstruction( "php", " print( " . __CLASS__ . "::translate( '" . addcslashes( trim( $node->saveXML( false ) ), "\\'" ) . "', array(" . ATalCompiler::implodeKeyed( $params ) . ")  , \$__tal->getTemplate()));" );
		$node->removeChilds();
		$node->appendChild( $pi );
	}
	
	public static function translate($str, array $params, $path) {
		return \ambient\i18n\I18nClass::t( $str, $params, $path );
	}
}