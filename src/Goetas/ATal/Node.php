<?php
namespace Goetas\ATal;

use goetas\xml;

Interface Node
{

    function visit(xml\XMLDomElement $node, ATal $atal);
}
