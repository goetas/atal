<?php
namespace Goetas\ATal\Attribute;

use Goetas\ATal\Attribute;
use Goetas\ATal\ATal;
use DOMAttr;
use Goetas\ATal\DOMHelper;

class Attribute_base implements Attribute
{

    function visit(DOMAttr $att, ATal $atal)
    {
        $node = $att->ownerElement;
        
        $pi = $node->ownerDocument->createTextNode("{% {$att->localName} " . html_entity_decode($att->value) . " %}");
        $node->parentNode->insertBefore($pi, $node);
        
        $pi = $node->ownerDocument->createTextNode("{% end{$att->localName} %}");
        DOMHelper::insertAfter($node->parentNode, $pi, $node);
        
        $node->removeAttributeNode($att);
    }
}