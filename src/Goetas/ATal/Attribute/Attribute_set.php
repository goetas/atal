<?php
namespace Goetas\ATal\Attribute;
use Goetas\ATal\Attribute;
use Goetas\ATal\ATal;
use DOMAttr;
class Attribute_set implements Attribute {

	function visit(DOMAttr $att, ATal $atal) {
		
		$node = $att->ownerElement;
		
		$pi = $node->ownerDocument->createTextNode ( "{% {$att->localName} ".html_entity_decode($att->value)." %}" );	
		$node->parentNode->insertBefore ( $pi, $node );
				
		$node->removeAttributeNode($att);
	}
}