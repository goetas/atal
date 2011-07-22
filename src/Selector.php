<?php
namespace goetas\atal;

abstract class Selector extends Plugin implements ISelector{
	/**
	 *
	 * @var xml\XMLDom
	 */
	protected $dom;
	/**
	 *
	 * @var Compiler
	 */
	protected $compiler;
	public function setDom(xml\XMLDom $dom){
		$this->dom = $dom;
	}
	public function setCompiler(Compiler $compiler){
		$this->compiler = $compiler;
	}
}

