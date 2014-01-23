<?php
namespace Goetas\ATal\Attribute;
use Goetas\ATal\Attribute;
use Goetas\ATal\ATal;
use DOMAttr;
use Goetas\ATal\DOMHelper;
class Attribute_capture implements Attribute {

	function visit(DOMAttr $att, ATal $atal) {
		
		$node = $att->ownerElement;
		
		$pi = $node->ownerDocument->createTextNode ( "{% set ".html_entity_decode($att->value)." %}" );	
		$node->parentNode->insertBefore ( $pi, $node );
		
		$pi = $node->ownerDocument->createTextNode ( "{% endset %}" );	
		
		DOMHelper::insertAfter($node->parentNode,$pi, $node);
		
		$node->removeAttributeNode($att);
	}
}