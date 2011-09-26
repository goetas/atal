<?php
namespace goetas\atal\filters;
use InvalidArgumentException;
use goetas\atal\xml;
class DomFilter extends Filter{
	/**
	 *
	 * @param $str xml\XMLDom
	 * @return void
	 */
	function applyFilters(xml\XMLDom $xml) {
		foreach ($this->getFilters() as $filter) {
			call_user_func($filter, $xml);
		}
	}
}
