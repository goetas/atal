<?php
abstract class ATalAttrCompilablePlugin extends ATalPlugin{
	/**
	 * Ferma l'elaborazione del contenuto del nodo
	 * @var int
	 */
	const STOP_NODE=1;
	/**
	 * Ferma l'elaborazione degli attributi del nodo
	 * @var int
	 */
	const STOP_ATTRIBUTE=2;
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