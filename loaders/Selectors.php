<?php
namespace goetas\atal\loaders;
use goetas\atal\Loader;
use goetas\atal\IModifier;
use goetas\atal\ATal;
use goetas\atal\Compiler;
class Selectors extends \goetas\pluginsys\Loader {
	/**
	 * @var ATal
	 */
	protected $tal;
	/**
	 * @var Compiler
	 */
	protected $compiler;
	public function __construct(ATal $tal, Compiler $compiler) {
		$this->compiler = $compiler;
		$this->tal = $tal;
		parent::__construct();
	}
	/**
	 * 
	 * @param string $modifier
	 * @return ISelector
	 */
	public function selector($selector) {
		$sel = $this->getPlugin($selector);
		$sel->setATal($this->tal);
		$sel->setCompiler($this->compiler);
		return $sel;
	}
	

}
