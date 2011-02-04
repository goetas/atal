<?php
namespace goetas\atal;
use Traversable;
use Exception;
use goetas\atal\xml;
use goetas\atal\Attribute;
use goetas\atal\ATal;
abstract class DynamicAttribute extends Attribute {
	protected $fatto = false; 
	public function prependPI() {
		if(!$this->fatto){
			$str =" require_once( '" . addslashes( __FILE__ ) . "'); \n";
			$pi = $this->dom->createProcessingInstruction( "php", $str );
			$this->dom->insertBefore( $pi, $this->dom->documentElement );
			$this->fatto = true;
		}
	}
	protected function getOptions() {
		return array();
	}
	function start(xml\XMLDomElement $node, \DOMAttr $att){
		$this->prependPI();
		$opt = $this->getOptions();
		foreach ( $node->query( ".//*[@t:id]/@t:id", array("t" => ATal::NS ) ) as $tt ){
			$tt->ownerElement->removeAttributeNode( $tt );
		}
		$str = str_replace(" xmlns=\"".$node->lookupNamespaceURI(null)."\"","", trim( $node->saveXML( false )) ) ;

		$parts = $this->compiler->splitExpression( $att->value, ";" );
	
		foreach ( $parts as $part ){
			if(preg_match( "/^([a-z_\\0-9]+)\\s*=\\s*(.*)/i", $part, $mch )){
				$mch [2] = trim( $mch [2] );
				$params [$mch [1]] = $this->compiler->parsedExpression(trim( $mch [2] ),true);
			}else{
				$params [] = $this->compiler->parsedExpression($part,true);
			}
		}
		$code .=" \\".get_class($this)."::run( " . $this->compiler->dumpKeyed( $params ) . " , '" . addcslashes( $str , "\\'" ) . "')";

		$pi = $this->dom->createProcessingInstruction( "php", "print( $code );");
		
		if(!$opt['preserveContent']){
			$node->removeChilds();
		}
		if($opt['prepend']){
			$node->prependChild( $pi );
		}else{
			$node->appendChild( $pi );
		}
		if($opt['preserveContent']){
			return null;
		}else{
			return self::STOP_NODE;
		}
	}
	
	static function run(array $params, $content){
		
	}
}
?>