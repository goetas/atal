<?php
namespace goetas\atal\loaders;
use goetas\atal\Loader;
use goetas\atal\IService;
use goetas\atal\ATal;
class Services extends \goetas\atal\Loader {
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
	 * @param string $service
	 * @return IService
	 */
	public function service($service) {
		$mod = $this->getPlugin($service);
		return $mod;
	}


}
