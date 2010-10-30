<?php
namespace goetas\atal;
use goetas\atal\xml;
use goetas\atal\CompilableAttribute;
use goetas\atal\ATal;
use goetas\atal\Compiler;
class BaseRuntimeAttribute extends CompilableAttribute{
	function start(xml\XMLDomElement  $node, \DOMAttr $att){
		$content = '';
		foreach ( $node->childNodes as $child ) {
			$content .= $node->ownerDocument->saveXML ( $child );
		}
		$params = array ();
		foreach ( $this->compiler->splitExpression ( $att->value, ";" ) as $part ) {
			list ( $k, $v ) = $this->compiler->splitExpression ( $part, "=" );
			if (strlen ( $v )) {
				$params [$k] = $v;
			} else {
				$params [] = $k;
			}
		}
		
		$code = "print ( ";
		$code .="\$__tal->getRuntimeAttributes()->attribute('{$att->localName}')->run('". addcslashes($content,"\\'")."' , ".var_export($params,1)." )"; 
		$code .=" ); " ;
		$pi = $node->ownerDocument->createProcessingInstruction ( "php", $code);
		
		$node->removeChilds ();
		$node->appendChild ( $pi );
		return self::STOP_NODE;
	}
}
?>