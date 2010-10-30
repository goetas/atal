<?php
namespace goetas\atal;

abstract class CompilableAttribute extends Plugin implements ICompilableAttribute{
	/**
	 * @var xml\XMLDom
	 */
	protected $dom;
	/**
	 * @var Compiler
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
