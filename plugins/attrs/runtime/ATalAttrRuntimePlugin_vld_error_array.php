<?php
class ATalAttrRuntimePlugin_vld_error_array extends ATalAttrRuntimePlugin {
	function run(array $params = array(), $content = '') {
		if(! count( $params )){
			throw new ATalException( "ATalAttrRuntimePlugin_vld_error: specificare almeno un parametro" );
		}
		
		$errorData = $params ["error"] ? $params ["error"] : $params [0];
		if(! is_array( $errorData ) && ! ($errorData instanceof Traversable)){
			return '';
		}
		$dom = new \goetas\xml\XMLDom( );
		foreach ( $errorData as $index => $errori ){
			$root = $dom->addChildNS( "Validation", "validation" )->setAttr( "index", $index );
			//main
			if($errori instanceof Exception){
				$root->addChildNS( "Validation", "main" )->setAttr( "value", $errori->getMessage() );
			}elseif(is_array( $errori ) && isset( $errori ["MAIN"] )){
				$root->addChildNS( "Validation", "main" )->setAttr( "value", $errori ["MAIN"] );
				unset( $errori ["MAIN"] );
			}
			//mesages
			if(($errori instanceof Traversable) || is_array( $errori )){
				foreach ( $errori as $key => $val ){
					$root->addChildNS( "Validation", "message" )->setAttr( "for", $key )->setAttr( "value", $val );
				}
			}
		}
		$ret = '';
		foreach ($dom->childNodes as $node){
			$ret.= $dom->saveXML($node);
		}
		return $ret;
	}
}
?>