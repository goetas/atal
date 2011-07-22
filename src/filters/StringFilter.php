<?php
namespace goetas\atal\filters;
use InvalidArgumentException;
class StringFilter extends Filter{
	/**
	 *
	 * @param string $str
	 * @return string
	 */
	function applyFilters($str) {
		foreach ($this->filters as $filter) {
			$str = call_user_func($filter, $str);
		}
		return $str;
	}
}