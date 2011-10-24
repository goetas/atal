<?php
namespace goetas\atal\extensions\fixcdata;


use goetas\atal\Extension;
use goetas\atal\Compiler;
use DOMCDATASection;
class FixCdata extends Extension{
	public function setupCompiler(Compiler $compiler) {

		$compiler->getPreXmlFilters()->addFilter(function($dom){

			$nodi = array();
			foreach ($dom->query("//text()") as $nodo){
				if($nodo instanceof DOMCDATASection){
					$nodi[]=$nodo;
				}
			}
			foreach ($nodi as $nodo){
				$fixcdata = $nodo->ownerDocument->createElementNS(__NAMESPACE__,"FixCdata");
				$nodo->parentNode->insertBefore($fixcdata, $nodo);
				$nodo->parentNode->removeChild($nodo);
				$fixcdata->appendChild($nodo);
			}
			return $dom;
		}, -10000);

		$compiler->getPreXmlFilters()->addFilter(function($dom){
			$nodi = array();
			foreach ($dom->query("//cd:FixCdata", array("cd"=>__NAMESPACE__)) as $nodo){
				$nodi[]=$nodo;
			}
			foreach ($nodi as $nodo){
				$cdata = $nodo->ownerDocument->createCDATASection($nodo->firstChild->data);
				$nodo->parentNode->insertBefore($cdata, $nodo);
				$nodo->parentNode->removeChild($nodo);
			}
			return $dom;
		}, 10000);

	}
}
