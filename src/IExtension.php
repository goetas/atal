<?php
namespace goetas\atal;
interface IExtension{
	public function setup(ATal $tal);
	public function setupCompiler(Compiler $compiler);
}