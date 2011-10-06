<?php
namespace goetas\atal\plugins\attributes;
use goetas\xml;
use Exception;
use goetas\atal\Attribute;
use goetas\atal\Compiler;
use goetas\atal\ATal;
class Attribute_block_def extends Attribute{
	function start(xml\XMLDomElement $node, \DOMAttr $att){

		$node->removeAttributeNode($att);


		$newNode = $node->ownerDocument->documentElement->addChildNs(ATal::NS, "atal-block");

		$piS = $this->dom->createProcessingInstruction( "php", "\nfunction {$att->value} (array \$__atal__scope  = array()) { \nextract(\$this->getData(), EXTR_SKIP); \n if(count(\$__atal__scope) ){ extract(\$__atal__scope, EXTR_OVERWRITE); }\n" );
		$piE = $this->dom->createProcessingInstruction( "php", " \n}\n " );


		$newNode->appendChild($piS);
		$newNode->addTextChild("\n");
		foreach ($node->childNodes as $nd){
			$newNode->appendChild($nd->cloneNode(true));
		}

		$newNode->appendChild($piE);

		$this->compiler->applyTemplates($newNode);


	}
}

