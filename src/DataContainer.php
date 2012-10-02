<?php
namespace goetas\atal;
/**
 * @author goetas
 *
 */
class DataContainer {
	protected $scope = array ();
	protected $data = array ();
	/**
	 *
	 */
	public function __construct(){
		$this->addScope ( );
	}

	function __clone() {
		$this->clear ();
	}
	/**
	 * Ritorna lo stack corrente
	 * @return array
	 */
	public function &getData() {
		return $this->data;
	}
	/**
	 * aggiungi uno stack
	 * @param array $vars
	 */
	public function addScope(array $vars = array()) {
		unset ( $vars ["this"], $vars ["__file"] );

		$scope = array_replace($this->data, $vars);

		$this->data = &$scope;
		$this->scope [] = &$scope;
	}
	/**
	 * rimuovi uno stack
	 */
	public function removeScope() {
		array_pop ( $this->scope );
		end ( $this->scope );
		$this->data = &$this->scope [key ( $this->scope )];
	}
	/**
	 * imposta una variabile nello stack corrente
	 * @param $varName
	 * @param $value
	 */
	function assign($varName, $value = null) {
		if ($varName != '') {
			return $this->data [$varName] = $value;
		}
		return null;
	}
	/**
	 * imposta una variabile nello stack corrente
	 * @param string $varName
	 * @param mixed $value
	 */
	public function __set($varName, $value) {
		$this->data [$varName] = $value;
	}
	/**
	 * Recupera una variabile dallo stack corrente
	 * @param string $varName
	 */
	public function &__get($varName) {
		return $this->data [$varName];
	}

	/**
	 * assigns values to template variables by reference
	 * @param string $tpl_var the template variable name
	 * @param mixed $value the referenced value to assign
	 */
	function assignByRef($varName, &$value) {
		if ($varName != '') {
			$this->data [$varName] = &$value;
		}
	}
	/**
	 * Svuota tutta lo stack
	 */
	public function clear() {
		$this->scope = array ();
		$this->data = array ();
		$this->addScope ();
	}
}