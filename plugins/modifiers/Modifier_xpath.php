<?php
namespace goetas\atal\plugins\modifiers;
use goetas\atal\Modifier;
class Modifier_xpath extends Modifier{
	function modify($str, array $params=array()){
		return self::xpathValue($str, $params);
	}
	public static function xpathValue($value, array $params){
		try {
			$value = "<atal_xpath:div xmlns:atal_xpath=\"Modifier_xpath\">{$value}</atal_xpath:div>";
			$xml = XMLDom::loadXMLString($value);
			$xpath = new XPath($xml);
			foreach ($params as $k => $v){
				if ($k !== intval($k)){
					$xpath->registerNamespace($k, $v);
				}
			}
			$res = $xpath->query($params[0], $xml->documentElement);
			foreach ($res as $nodo){
				$str .= $nodo->saveXML();
			}
			return $str;
		} catch (DOMException $e){
			return '';
		}
	}
}