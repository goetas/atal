<?php
namespace goetas\atal;
class TemplateRef {
	protected $realpath;
	/**
	 * @var TemplateRef
	 */
	protected $parent;
	protected $tpl;
	protected $tipo;
	protected $query;
	/**
	 * @var ATal
	 */
	protected $tal;
	/**
	 * @return the $realpath
	 */
	public function getRealpath() {
		return $this->realpath;
	}

	/**
	 * @param field_type $realpath
	 */
	public function setRealpath($realpath) {
		$this->realpath = $realpath;
	}

	/**
	 * @return the $mtime
	 */
	public function __construct(ATal $tal, $path, TemplateRef $parent = null) {
		$this->tal = $tal;
		$this->parent = $parent;
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
	/**
	 * @return TemplateRef
	 */
	public function getParent() {
		return $this->parent;
	}
	public function getBaseName() {
		return $this->tpl;
	}
	public function setBaseName($tpl) {
		$this->tpl = $tpl;
	}
	public function getSelectorType() {
		return $this->tipo;
	}
	public function getSelectorQuery() {
		return $this->query;
	}
	public function __toString() {
		if(!$this->tipo){
			return $this->tpl;
		}else{
			return "$this->tpl#$this->tipo:$this->query";
		}
	}
}
