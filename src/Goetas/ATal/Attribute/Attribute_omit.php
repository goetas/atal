<?php
namespace Goetas\ATal\Attribute;

use Goetas\ATal\Attribute;
use Goetas\ATal\ATal;
use DOMAttr;
use Goetas\ATal\DOMHelper;

class Attribute_omit implements Attribute
{

    function visit(DOMAttr $att, ATal $atal)
    {
        $node = $att->ownerElement;
        
        $pi = $node->ownerDocument->createTextNode("{% set _tmp_omit = " . html_entity_decode($att->value) . " %}");
        $node->parentNode->insertBefore($pi, $node);
        
        $pi = $node->ownerDocument->createTextNode("{% if not _tmp_omit %}");
        $node->parentNode->insertBefore($pi, $node);
        
        $pi = $node->ownerDocument->createTextNode("{% if not _tmp_omit %}");
        $node->appendChild($pi);
        
        $pi = $node->ownerDocument->createTextNode("{% endif %}");
        DOMHelper::insertAfter($node->parentNode, $pi, $node);
        
        $node->removeAttributeNode($att);
    }
}