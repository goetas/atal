<?php
namespace goetas\atal\plugins\attributes\compilable;
use goetas\atal\xml;
use goetas\atal\CompilableAttribute;
class CompilableAttribute_translate extends CompilableAttribute {
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
				$options[substr($k,1)]=$v;
			}else{
				$params [$k] = $this->compiler->parsedExpression( $v );
			}
		}
		foreach ( $node->query( ".//*[@t:id]/@t:id", array("t" => ATal::NS ) ) as $tt ){
			$tt->ownerElement->removeAttributeNode( $tt );
		}

		$nsp = " xmlns=\"".$node->lookupNamespaceURI(null)."\"";

		$str = str_replace($nsp,"", trim( $node->saveXML( false )) ) ;

		$pi = $this->dom->createProcessingInstruction( "php", " print( " . __CLASS__ . "::translate( '" . addcslashes( $str , "\\'" ) . "', array(" . ATalCompiler::implodeKeyed( $params ) . ")  , $domain ,array(" . ATalCompiler::implodeKeyed( $options ) . ")));" );

		$node->removeChilds();
		$node->appendChild( $pi );
		return self::STOP_NODE;
	}

	public static function translate($str, array $params, $path, $options=array()) {
		if($options["inject"]){
			$params = array_merge(array_filter($options["inject"],array(__CLASS__,'soloPrimitivi')),$params);
		}
		$t = \ambient\i18n\I18nClass::t( $str, $params, $path );
		if($options["nl2br"]){
			return nl2br(self::checkHtml(trim($t)));
		}else{
			return self::checkHtml($t);
		}
	}
	public static function checkHtml($s) {
		if(strpos($s,"&")!==false){
			return preg_replace("/&(?![a-z]+;)/i","&amp;",$s);  // in caso che i traduttori sbaglino, sistemo le "&" con la relativa entita html
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