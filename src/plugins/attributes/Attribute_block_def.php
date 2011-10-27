<?php
namespace goetas\atal\plugins\attributes;
use goetas\xml;
use Exception;
use goetas\atal\Attribute;
use goetas\atal\Compiler;
use goetas\atal\ATal;
class Attribute_block_def extends Attribute{
	function start(xml\XMLDomElement $node, \DOMAttr $att){

		$piS = $this->dom->createProcessingInstruction( "php", "\nfunction {$att->value} (array \$__atal__scope  = array()) {\n\textract(\$__atal__scope, EXTR_OVERWRITE);\n" );
		$piE = $this->dom->createProcessingInstruction( "php", " \n}\n " );

		$newNode = $node->ownerDocument->documentElement->addChildNs(ATal::NS, "atal-block");

		$newNode->appendChild($piS);
		$newNode->addTextChild("\n");



		$tomittedNode = $newNode->addChildNs(ATal::NS, "atal-block-omit");
		$node->removeAttributeNode($att);
		foreach ( $node->attributes as $attNode ) {
			if($attNode->namespaceURI==ATal::NS && $attNode->name!='block-call'){
				$tomittedNode->setAttributeNS (ATal::NS, $attNode->name, $attNode->value);
			}
		}
		$tomittedNode->setAttributeNS(ATal::NS, "omit", "true");

		foreach ($node->childNodes as $nd){
			$tomittedNode->appendChild($nd->cloneNode(true));
		}

		$newNode->appendChild($piE);

		$this->compiler->applyTemplates($tomittedNode);

	}
}

