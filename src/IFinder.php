<?php 
namespace goetas\atal;
interface IFinder{
	public function getTemplate($name);
	public function getCacheName($name);
	public function isFresh($name, $current);
	public function getRelativeTo($name, $base);
}