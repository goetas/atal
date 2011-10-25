<?php
namespace goetas\atal;
use goetas\xml;
interface ISelector extends IPlugin{
	function setDom(xml\XMLDom $dom);
	/**
	 *
	 * @param string $query
	 */
	function select($query);
}

