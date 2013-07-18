<?php 
namespace goetas\atal\finders;

use goetas\atal\TemplateRef;
use goetas\atal\Template;

use goetas\atal\IFinder;
use goetas\atal\FinderException;
class Aggregate {
	protected $finders = array();
	public function __construct(array $finders = array()) {
		array_map(array($this, 'addFinder'), array_reverse($finders));
	}
	public function addFinder(IFinder $finder) {
		$this->finders[spl_object_hash($finder)] = $finder;
	}
	public function remFinder(IFinder $finder) {
		unset($this->finders[spl_object_hash($finder)]);
	}
	public function getFinders() {
		return $this->finders;
	}
	public function setFinders(array $finders = array()) {
		$this->finders = array();
		array_map(array($this, 'addFinder'), array_reverse($finders));
	}
	public function getTemplate(TemplateRef $templateRef, &$finderRef = null){
		foreach (array_reverse($this->finders) as $finder){
			try {
				$res = $finder->getTemplate($templateRef);
				$finderRef = $finder;
				return $res;
			}catch (FinderException $e){
			}
		}
		throw new FinderException(__METHOD__." Non riesco a trovare il template '$templateRef'");
	}
	public function getCacheName(TemplateRef $templateRef, &$finderRef = null){
		foreach (array_reverse($this->finders) as $finder){
			try {
				$res = $finder->getCacheName($templateRef);
				$finderRef = $finder;
				return $res; 
			}catch (FinderException $e){
			}
		}
		throw new FinderException(__METHOD__." Non riesco a trovare il template '$templateRef'");
	}
	public function isFresh(TemplateRef $templateRef, $current, &$finderRef = null){
		foreach (array_reverse($this->finders) as $finder){
			try {
				$res = $finder->isFresh($templateRef, $current); 
				$finderRef = $finder;
				return $res; 
			}catch (FinderException $e){
			}
		}
		throw new FinderException(__METHOD__." Non riesco a trovare il template '$templateRef'");
	}
}
