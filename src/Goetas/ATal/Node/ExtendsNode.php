<?php
namespace Goetas\ATal\Node;
use Goetas\ATal\Node;
use goetas\xml;
use Goetas\ATal\ATal;
use Goetas\ATal\DOMHelper;
class ExtendsNode implements Node {
	function visit(xml\XMLDomElement $node, ATal $atal){

		if(!$node->hasAttribute("name") && !$node->hasAttribute("name-exp")){
			throw new Exception("name or name-exp atribute is required");
		}
		
		$atal->applyTemplatesToChilds($node);
		
		$pi = $node->ownerDocument->createTextNode ( "{% extends ". ($node->hasAttribute("name-exp")?$node->getAttribute("name-exp"):("'".$node->getAttribute("name")."'")) ." %}" );		
		DOMHelper::insertAfter($node->parentNode,$pi, $node);
		$ref = $pi;
		while($child = $node->firstChild){
			$node->removeChild($child);
			DOMHelper::insertAfter($node->parentNode, $child, $ref);
			$ref = $child;
		}
		$node->remove();	
	}

}