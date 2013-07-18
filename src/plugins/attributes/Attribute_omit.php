<?php
namespace goetas\atal\plugins\attributes;
use goetas\xml;
use goetas\atal\Attribute;
class Attribute_omit extends Attribute {
	static $cnt = 0;
	function start(xml\XMLDomElement $node, \DOMAttr $att) {

		if($att->value=="1" || strtolower(trim($att->value))=="true"){

			$ref = $node;
			$nodes = array();
			foreach ( $node->childNodes as $subNode ){
				$nodes [] = $subNode;
			}
			foreach ( $nodes as $subNode ){
				$node->removeChild($subNode);
				$node->parentNode->insertAfter( $subNode, $ref );
				$ref = $subNode;
			}
			$node->parentNode->removeChild($node);

			foreach ( $nodes as $newNode ){
				if($newNode instanceof xml\XMLDomElement){
					$this->compiler->applyTemplates( $newNode );
				}elseif($newNode instanceof \DOMText){
					$this->compiler->applyTextVars( $newNode );
				}
			}
			return self::STOP_NODE;

		}else{

			$piIf = $this->dom->createProcessingInstruction( "php", "if( !( $att->value ) ){ // omit " . self::$cnt . " " );
			$piElse = $this->dom->createProcessingInstruction( "php", "} else {  // omit " . self::$cnt . " " );
			$piEndIf = $this->dom->createProcessingInstruction( "php", "}  // omit " . self::$cnt . " " );
			self::$cnt ++;

			$node->parentNode->insertBefore( $piIf, $node );

			$node->parentNode->insertAfter( $piElse, $node );

			$node->parentNode->insertAfter( $piEndIf, $piElse );

			$nodes = array();
			foreach ( $node->childNodes as $subNode ){
				$nodes [] = $newRef = $subNode->cloneNode( true );
				$node->parentNode->insertBefore( $newRef, $piEndIf );
			}
			foreach ( $nodes as $newNode ){
				if($newNode instanceof xml\XMLDomElement){
					$this->compiler->applyTemplates( $newNode );
				}elseif($newNode instanceof \DOMText){
					$this->compiler->applyTextVars( $newNode );
				}
			}
		}

	}
}
