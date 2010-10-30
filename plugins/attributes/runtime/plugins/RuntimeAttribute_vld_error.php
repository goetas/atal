<?php
namespace goetas\atal\plugins\attributes\runtime\plugins;
use goetas\atal\xml;
use goetas\atal\IRuntimeAttribute;
use Traversable;
use Exception;
class RuntimeAttribute_vld_error extends Plugin implements IRuntimeAttribute {
	function run(array $params, $content) {
		
		if (! count ( $params )) {
			throw new Exception ( "AttrRuntimePlugin_vld_error: specificare almeno un parametro" );
		}
		
		$errorData = $params ["error"] ? $params ["error"] : $params [0];
		
		$dom = new xml\XMLDom ();
		$root = $dom->addChildNS ( "Validation", "validation" );
		
		if (isset ( $params ["index"] )) {
			$root->setAttr ( "index", $params ["index"] );
		} else {
			$root->setAttr ( "index", "__MAIN" );
		}
		if ($errorData instanceof Exception) {
			$root->addChildNS ( "Validation", "main" )->setAttr ( "value", $errorData->getMessage () );
		}
		if (is_array ( $errorData ) && isset ( $errorData ["MAIN"] )) {
			$root->addChildNS ( "Validation", "main" )->setAttr ( "value", $errorData ["MAIN"] );
			unset ( $errorData ["MAIN"] );
		}
		if (($errorData instanceof Traversable) || is_array ( $errorData )) {
			foreach ( $errorData as $key => $val ) {
				$root->addChildNS ( "Validation", "message" )->setAttr ( "for", $key )->setAttr ( "value", $val );
			}
		}
		
		return $dom->saveXML ( $root );
	}
}
?>