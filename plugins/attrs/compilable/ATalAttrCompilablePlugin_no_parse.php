<?php
class ATalAttrCompilablePlugin_no_parse extends ATalAttrCompilablePlugin {
	function start(ATal_XMLDomElement $node, $attValue) {
		foreach ( $node->query( ".//*[@t:id]/@t:id", array("t" => ATal::NS ) ) as $tt ){
			$tt->ownerElement->removeAttributeNode( $tt );
		}
		return self::STOP_NODE;
	}

}