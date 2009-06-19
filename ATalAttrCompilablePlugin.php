<?php
abstract class ATalAttrCompilablePlugin extends ATalPlugin{
	/**
	 * @var XMLDom
	 */
	protected $dom;
	/**
	 * @var ATalCompiler
	 */
	protected $compiler;
	abstract function start(XMLDomElement $node, $attValue);
	function __construct(ATalCompiler $compiler, XMLDom $dom){	
		$this->compiler=$compiler;
		$this->dom=$dom;
		$this->init();
	}
	protected function init(){
	}
	
}

?>