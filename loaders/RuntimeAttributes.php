<?php
namespace goetas\atal\loaders;
use goetas\atal\Loader;
use goetas\atal\IRuntimeAttribute;
use goetas\atal\ATal;
class RuntimeAttributes extends \goetas\pluginsys\Loader {
	/**
	 * @var ATal
	 */
	protected $tal;
	public function __construct(ATal $tal) {
		$this->tal = $tal;
		parent::__construct();
	}
	/**
	 * 
	 * @param string $modifier
	 * @return IRuntimeAttribute
	 */
	public function attribute($attribute) {
		$attr = $this->getPlugin($attribute);
		$attr->setATal($this->tal);
		return $attr;
	}
	

}
