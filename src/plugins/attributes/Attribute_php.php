<?php
namespace goetas\atal\plugins\attributes;
use goetas\atal\ATal;

use goetas\xml;
use goetas\atal\Attribute;
class Attribute_php extends Attribute{
	function start(xml\XMLDomElement $node, \DOMAttr $att){
		$phps = $node->query(".//t:php", array("t"=>ATal::NS));
		$nds = array();
		foreach ($phps as $nd){
			$nds[]=$nd;
		}

		foreach ($nds as $nd){
			$php = trim(htmlspecialchars_decode($nd->saveXML(false)), "\n");

			$splited = preg_split( $this->compiler->getCurrRegex(), $php );
			$mch = array();

			if(count($splited)>1){
				preg_match_all ( $this->compiler->getCurrRegex(), $php, $mch );
			}

			$n = array();
			foreach ($splited as $k => $v){
				if(strlen($v)){
					$n[]="'".addcslashes($v, "'\\")."'";
				}
				if(isset($mch[1]) && isset($mch[1][$k])){
					$n[]=$this->compiler->parsedExpression ( $mch[1][$k]);
				}

			}
			$new = $nd->ownerDocument->createTextNode(implode(".", $n));
			$nd->parentNode->replaceChild($new, $nd);

		}

		$pi = $this->dom->createProcessingInstruction("php", "\n".htmlspecialchars_decode($node->saveXML(false)));
		$node->parentNode->replaceChild($pi, $node);

		return self::STOP_NODE | self::STOP_ATTRIBUTE;
	}
}
