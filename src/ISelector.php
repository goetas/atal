<?php
namespace goetas\atal;

interface ISelector extends IPlugin{
	function setDom(xml\XMLDom $dom);
	/**
	 *
	 * @param string $query
	 */
	function select($query);
}

