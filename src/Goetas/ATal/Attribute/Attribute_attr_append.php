<?php
namespace Goetas\ATal\Attribute;
use Goetas\ATal\Attribute;
use DOMAttr;
class Attribute_attr_append extends Attribute_attr {
	protected function getSetExpression($varName, $attName, $expr){
		return "{% set {$varName}.{$attName} = {$varName}.{$attName}|default([])|merge($expr); %}\n";
	}
}