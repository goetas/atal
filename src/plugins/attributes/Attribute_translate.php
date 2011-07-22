<?php
namespace goetas\atal\plugins\attributes;
use goetas\atal\xml;
use goetas\atal\Attribute;
use goetas\atal\ATal;
class Attribute_translate extends Attribute {
	function start(xml\XMLDomElement $node, \DOMAttr $att) {
		$examine = $node;
		$domain = "null";
		do{
			if(is_array($examine->attributes)){
				foreach ($examine->attributes as $attr) {
					if($attr->namespaceURI == ATal::NS && $attr->locanName == 'translate-domain'){
						$domain = "'".addcslashes($attr->value,"\\'")."'";
						break 2;
					}
				}
			}
			$examine = $examine->parentNode;
		}while($examine);


		$parts = $this->compiler->splitExpression( $att->value, ";" );
		$params = array();
		$options=array();
		foreach ( $parts as $part ){
			list ( $k, $v ) = $this->compiler->splitExpression( $part, "=" );
			if($k[0]==":"){
				if(is_null($v)){
					$v = true;
				}
				$options[substr($k,1)]=$v;
			}else{
				$params [$k] = $this->compiler->parsedExpression( $v , true);
			}
		}
		foreach ( $node->query( ".//*[@t:id]/@t:id", array("t" => ATal::NS ) ) as $tt ){
			$tt->ownerElement->removeAttributeNode( $tt );
		}

		$nsp = " xmlns=\"".$node->lookupNamespaceURI(null)."\"";

		$str = str_replace($nsp,"", trim( $node->saveXML( false )) ) ;


		$code ="";
		if($options["nl2br"]){
			$code .=" nl2br( ";
		}
		$code .=" \\".__CLASS__."::checkHtml(\$this->getTal()->getServices()->service('goetas\\\\atal\\\\plugins\\\\services\\\\translate\\\\ITranslate')->translate('" . addcslashes( $str , "\\'" ) . "', " . $this->compiler->dumpKeyed( $params ) . "  , $domain , " . var_export( $options,1 ) . " ))";

		if($options["nl2br"]){
			$code .=" ) ";
		}
		$pi = $this->dom->createProcessingInstruction( "php", "print( $code );");

		$node->removeChilds();
		$node->appendChild( $pi );
		return self::STOP_NODE;
	}
	public static function checkHtml($s) {
		if(strpos($s,"&")!==false){
			return preg_replace("/&(?![a-z]+;)/i","&amp;", $s);  // in caso che i traduttori sbaglino, sistemo le "&" con la relativa entita html
		}else{
			return $s;
		}
	}
	public static function soloPrimitivi($v){
		if(is_scalar($v) || !$v){
			return true;
		}
		if(is_object($v) && method_exists( $v  , "__toString" )){
			return true;
		}
		return false;
	}
}