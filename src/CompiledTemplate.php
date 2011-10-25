<?php
namespace goetas\atal;
class CompiledTemplate extends DataContainer {
	/**
	 * @var IFinder
	 */
	private $finder;
	/**
	 * @return TemplateRef
	 */
	private $templateRef;
	/**
	 * @return ATal
	 */
	private $tal;
	protected $modifiers;
	protected $pluginVars= array();
	/**
	 * @return the $finder
	 */
	protected function getFinder() {
		return $this->finder;
	}

	/**
	 * @return the $templateRef
	 */
	protected function getTemplateRef() {
		return $this->templateRef;
	}

	public function __construct(ATal $tal, TemplateRef  $templateRef, IFinder $finder) {
		parent::__construct();
		$this->templateRef = $templateRef;
		$this->finder = $finder;
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
