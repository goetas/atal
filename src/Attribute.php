<?php
namespace goetas\atal;
use goetas\xml;
abstract class Attribute extends Plugin implements IAttribute{
	/**
	 * @var xml\XMLDom
	 */
	protected $dom;
	/**
	 * @var \goetas\atal\Compiler
	 */
	protected $compiler;
	/**
	 * @var ATal
	 */
	protected $tal;
	function setCompiler(Compiler $compiler){
		$this->compiler=$compiler;
		$this->setATal($compiler->getATal());
	}
	function setDom(xml\XMLDom $dom){
		$this->dom = $dom;
	}
	public function end(xml\XMLDomElement $node, \DOMAttr $att) {

	}

}
