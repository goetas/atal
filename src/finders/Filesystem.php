<?php
namespace goetas\atal\finders;

use goetas\atal\TemplateRef;
use goetas\atal\Template;

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
	public function getCacheName(TemplateRef $templateRef){
		return $this->getPath($templateRef);
	}
	public function isFresh(TemplateRef $templateRef, $current){
		return filemtime($this->getPath($templateRef)) < $current;
	}
	protected static function isAbsolutePath($path) {
		return ($path [0] == "/" || substr( $path, 0, 2 ) == "\\\\" || preg_match( "#^[a-z]:\\\\#i", $path ) || preg_match( "#^[a-z-0-9-\\.]+://#i", $path ));
	}
	protected function getPath(TemplateRef $templateRef){
		if($templateRef->getRealPath()!==null){
			return $templateRef->getRealPath();
		}
		
		$name = $templateRef->getBaseName();
		
		if(self::isAbsolutePath($name)){
			$file = $name;
		}else{
			if($templateRef->getParent()!==null){
				$base = dirname($templateRef->getParent()->getRealPath());
			}else{
				$base = null;
			}
			
			if($base===null){
				$base = $this->baseDir;
			}
			if($base=='.' || $base==''){
				$dir = getcwd();
			}else{
				$dir = $base;
			}
			$file = rtrim($dir, "\\/").DIRECTORY_SEPARATOR.$name;
		}
		if(is_file($file)){
			$templateRef->setRealPath($file);
			return $file;
		}
		throw new FinderException("Non riesco a trovare il template '$name', cercato '$dir/$name'");
	}
	public function getTemplate(TemplateRef $templateRef){
		$fullName = $this->getPath($templateRef);
		$template = new Template($templateRef, $this);
		$template->setContent(file_get_contents($fullName));
		$template->setFullName($fullName);
		return $template;
	}
}
