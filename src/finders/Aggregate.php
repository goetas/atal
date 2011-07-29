<?php 
namespace goetas\atal\finders;
use goetas\atal\IFinder;
use goetas\atal\FinderException;
class Aggregate implements IFinder{
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
	public function getTemplate($name){
		foreach (array_reverse($this->finders) as $finder){
			try {
				return $finder->getTemplate($name);
			}catch (FinderException $e){
			}
		}
		throw new FinderException(__METHOD__." Non riesco a trovare il template '$name'");
	}
	public function getCacheName($name){
		foreach (array_reverse($this->finders) as $finder){
			try {
				return $k.DIRECTORY_SEPARATOR.$finder->getCacheName($name);
			}catch (FinderException $e){
			}
		}
		throw new FinderException(__METHOD__." Non riesco a trovare il template '$name'");
	}
	public function isFresh($name, $current){
		foreach (array_reverse($this->finders) as $finder){
			try {
				return $finder->isFresh($name, $current);
			}catch (FinderException $e){
			}
		}
		throw new FinderException(__METHOD__." Non riesco a trovare il template '$name'");
	}
	public function getRelativeTo($name, $current){
		foreach (array_reverse($this->finders) as $finder){
			try {
				return $finder->getRelativeTo($name, $current);
			}catch (FinderException $e){
			}
		}
		throw new FinderException(__METHOD__." Non riesco a trovare il template '$name' from '$current'");
	}
}
