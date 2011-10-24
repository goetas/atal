<?php
namespace goetas\atal\finders;
use goetas\atal\IFinder;
use goetas\atal\FinderException;
class Filesystem implements IFinder{
	protected $baseDir;
	public function __construct($baseDir) {
		$this->baseDir = rtrim($baseDir, "\\/");
		if(!$this->baseDir){
			$this->baseDir = '.';
		}
	}
	public function getTemplate($name){
		return file_get_contents($this->getPath($name));
	}
	public function getCacheName($name){
		return $this->getPath($name);
	}
	public function isFresh($name, $current){
		return filemtime($this->getPath($name)) < $current;
	}
	protected static function isAbsolutePath($path) {
		return ($path [0] == "/" || substr( $path, 0, 2 ) == "\\\\" || preg_match( "#^[a-z]://#i", $path ) || preg_match( "#^[a-z-0-9-\.]+://#i", $path ));
	}
	protected function getPath($name) {
		if(self::isAbsolutePath($name) && is_file($name)){
			return $name;
		}elseif($this->baseDir=='.' && is_file($name)){
			return getcwd().DIRECTORY_SEPARATOR.$name;
		}elseif(is_file($this->baseDir.DIRECTORY_SEPARATOR.$name)){
			return $this->baseDir.DIRECTORY_SEPARATOR.$name;
		}

		throw new FinderException("Non riesco a trovare il template '$name'");
	}
	public function getRelativeTo($fullName, $base){
		$pos = strpos($fullName, "#");
		if($pos!==false){
			$name = substr($fullName, 0,$pos);
			$hash = substr($fullName, $pos);
		}else{
			$name = $fullName;
			$hash = '';
		}
		if(self::isAbsolutePath($base)){
			$dir = dirname($base);
		}elseif($this->baseDir=='.'){
			$dir = getcwd().DIRECTORY_SEPARATOR.dirname($base);
		}else{
			$dir = $this->baseDir.DIRECTORY_SEPARATOR.dirname($base);
		}
		$dir = rtrim($dir, "\\/");
		if(is_file($dir.DIRECTORY_SEPARATOR.$name)){
			return ($dir.DIRECTORY_SEPARATOR.$name).$hash;
		}
		throw new FinderException("Non riesco a trovare il template '$name'");
	}

}
