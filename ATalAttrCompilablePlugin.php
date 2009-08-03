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
	abstract function start(ATal_XMLDomElement  $node, $attValue);
	function __construct(ATalCompiler $compiler, ATal_XMLDom $dom){
		$this->compiler=$compiler;
		$this->dom=$dom;
		$this->init();
	}
	protected function init(){
	}

}

?>