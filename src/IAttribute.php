<?php
namespace goetas\atal;
Interface IAttribute extends IPlugin{
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

	function setCompiler(Compiler $compiler);
	function setDom(xml\XMLDom $dom);

	function start(xml\XMLDomElement  $node, \DOMAttr $att);
	function end(xml\XMLDomElement $node, \DOMAttr $att);

}
