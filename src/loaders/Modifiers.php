<?php
namespace goetas\atal\loaders;
use goetas\atal\Loader;
use goetas\atal\IModifier;
use goetas\atal\ATal;
class Modifiers extends \goetas\pluginsys\Loader {
	protected $defaultModifier;
	/**
	 * @var ATal
	 */
	protected $tal;
	public function __construct(ATal $tal, $defaultModifier) {
		$this->tal = $tal;
		$this->defaultModifier = $defaultModifier;
		parent::__construct();
		$this->addInitializer( function($modifier)use($tal){
			$modifier->setATal($tal);
		});
	}
	/**
	 * 
	 * @param string $modifier
	 * @return IModifier
	 */
	public function modifier($modifier) {
		if(!$modifier){
			$modifier = $this->defaultModifier;
		}
		return $this->getPlugin($modifier,true);
	}
	

}
