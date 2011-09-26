<?php
namespace goetas\atal;
class CompiledTemplate extends DataContainer {
	protected $tal;
	protected $modifiers;
	protected $pluginVars= array();
	public function __construct(ATal $tal) {
		parent::__construct();
		$this->tal = $tal;
		$this->modifiers = $tal->getModifiers();
		$this->pluginVars = $tal->getPluginVars();
		$this->init();
	}
	public function getTal() {
		return $this->tal;
	}
	public function init() {
		;
	}
}
