<?php
namespace Goetas\ATal\Node;

use Goetas\ATal\Node;
use Goetas\ATal\ATal;
use goetas\xml;
use Goetas\ATal\Exception;

class ImportNode implements Node
{

    function visit(xml\XMLDomElement $node, ATal $atal)
    {
        if (! $node->hasAttribute("name")) {
            throw new Exception("Name atribute is required");
        }
        if (! $node->hasAttribute("as") && ! $node->hasAttribute("aliases")) {
            throw new Exception("As or Alias atribute is required");
        }
        
        if ($node->hasAttribute("as")) {
            $pi = $node->ownerDocument->createTextNode("{% import " . ($node->getAttribute("name-exp") ? $node->getAttribute("name-exp") : ("'" . $node->getAttribute("name") . "'")) . " as " . $node->getAttribute("as") . " %}");
        } else {
            $pi = $node->ownerDocument->createTextNode("{% from " . ($node->hasAttribute("name-exp") ? $node->getAttribute("name-exp") : ("'" . $node->getAttribute("name") . "'")) . " import as " . $node->getAttribute("aliases") . " %}");
        }
        
        $node->parentNode->replaceChild($pi, $node);
    }
}