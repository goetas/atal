<?php
namespace goetas\atal\loaders;
use goetas\atal\Loader;
use goetas\atal\Compiler;
use goetas\atal\ATal;
use goetas\atal\IModifier;
use goetas\atal\IAttribute;
use goetas\atal\xml;
class Attributes extends \goetas\pluginsys\Loader {
	/**
	 * @var Compiler
	 */
	protected $compiler;
	/**
	 * @var ATal
	 */
	protected $tal;

	public function __construct(ATal $tal, Compiler $compiler) {
		$this->compiler = $compiler;
		$this->tal = $tal;
		parent::__construct();
	}
	/**
	 *
	 * @param string $attname
	 * @param xml\XMLDom $dom
	 * @return IAttribute
	 */
	public function attribute($attname) {
		$attPlugin = $this->getPlugin($attname,true);

		$attPlugin->setATal($this->tal);
		$attPlugin->setCompiler($this->compiler);

		return $attPlugin;
	}


}
