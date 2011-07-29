<?php
namespace goetas\atal;
class CompiledTemplate extends DataContainer {
	protected $tal;
	protected $modifiers;
	public function __construct(ATal $tal) {
		parent::__construct();
		$this->tal = $tal;
		$this->modifiers = $tal->getModifiers();
	}
	public function getTal() {
		return $this->tal;
	}
}
