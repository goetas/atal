<?php
namespace goetas\atal;
class Template {
protected $tpl;
	protected $tipo; 
	protected $query;  
	public function __construct($path) {
		list( $this->tpl,  $this->tipo, $this->query) = $this->parseUriParts($path);
	}
	protected function parseUriParts($path) {
		list ( $tpl, $query ) = explode ( '#', $path, 2 );
		$mch = array ();
		$tipo = null;

		if (strlen ( $query ) && preg_match ( "/^([a-z]+)\\s*:(.+)$/i", $query, $mch )) {
			$tipo = $mch [1];
			$query = $mch [2];
		} elseif (strlen ( $query )) {
			$tipo = "id";
		}
		return array (trim ( $tpl ), $tipo, $tipo ? $query : null );
	}
	public function getBaseName() {
		return $this->tpl;
	}
	public function getSelectorType() {
		return $this->tipo;
	}
	public function getSelectorQuery() {
		return $this->query;
	}
	public function __toString() {
		return "$this->tpl,  $this->tipo, $this->query";
	}
}
