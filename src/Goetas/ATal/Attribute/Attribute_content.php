<?php
namespace Goetas\ATal\Attribute;

use Goetas\ATal\Attribute;
use Goetas\ATal\ATal;
use DOMAttr;

class Attribute_content implements Attribute
{

    function visit(DOMAttr $att, ATal $atal)
    {
        $node = $att->ownerElement;
        $node->removeChilds();
        $pi = $node->ownerDocument->createTextNode("{{ " . html_entity_decode($att->value) . " }}");
        $node->appendChild($pi);
        
        $node->removeAttributeNode($att);
    }
}