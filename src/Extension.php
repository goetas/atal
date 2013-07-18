<?php
namespace goetas\atal;
abstract class Extension implements IExtension{
	public function setup(ATal $tal){
	}
	public function setupCompiler(Compiler $compiler){
	}
}