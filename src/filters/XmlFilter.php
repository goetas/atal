<?php
namespace goetas\atal\filters;
use InvalidArgumentException;
use goetas\atal\xml;
class XmlFilter extends Filter{
	/**
	 *
	 * @param $str xml\XMLDom
	 * @return xml\XMLDom
	 */
	function applyFilters(xml\XMLDom $xml) {
		foreach ($this->filters as $filter) {
			$xml = call_user_func($filter, $xml);
			if(!($xml instanceof xml\XMLDom)){
				throw new InvalidArgumentException ( "Return type of callback must be instanceof goetas\\atal\\xml\\XMLDom ");
			}
		}
		return $xml;
	}
}