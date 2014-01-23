<?php
namespace Goetas\ATal\Node;
use Goetas\ATal\Node;
use Goetas\ATal\ATal;
use goetas\xml;
use Goetas\ATal\Exception;
class IncludeNode implements Node {

	function visit(xml\XMLDomElement $node, ATal $atal){
		if(!$node->hasAttribute("name") && !$node->hasAttribute("name-exp")){
			throw new Exception("Name or name-exp atribute is required");	
		}

		$code = "{% include ";
		$code .= ($node->hasAttribute("name-exp")?$node->getAttribute("name-exp"):("'".$node->getAttribute("name")."'"));
		$code .= $node->getAttribute("ignore-missing")?" ignore missing":"";
		$code .= $node->hasAttribute("with")?(" with ".$node->getAttribute("with")):"";
		$code .= $node->getAttribute("sandboxed")=="true"?" sandboxed = true ":"";
		$code .= " %}";
		$pi = $node->ownerDocument->createTextNode ( $code );
		
		$node->parentNode->replaceChild ( $pi, $node );
		
	}

}