<?php
namespace goetas\atal\plugins\services\translate;

interface ITranslate{
	public function translate($str, array $args = array());
}