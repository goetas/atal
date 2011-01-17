<?php
namespace goetas\atal\plugins\attributes;
use goetas\atal\xml;
use goetas\atal\Attribute;
class Attribute_foreach extends Attribute{
	protected $fatto = false; 
	public function prependPI() {
		if(!$this->fatto){
			$str ="\nrequire_once( '" . addslashes( __FILE__ ) . "');\n";
			$str .="if(!(\$__foreach instanceof \\".__NAMESPACE__."\\Attribute_foreach_helper)) {\n\t\$__foreach = new \\".__NAMESPACE__."\\Attribute_foreach_helper();\n}";
			
			$pi = $this->dom->createProcessingInstruction( "php", $str );
			$this->dom->insertBefore( $pi, $this->dom->documentElement );
			$this->fatto = true;
		}
	}
	function start(xml\XMLDomElement $node, \DOMAttr $att){
		$this->prependPI();
		
		$name = uniqid('l');
		$loopName =  null;
		if(preg_match("/^([a-zA-Z_0-9]+)\\s*:\\s*([^:]+)/",$att->value,$mch) ){
			$loopName = "'".$mch[1]."'";
			$att->value = trim($mch[2]);
		}
		
		$mch = $this->compiler->splitExpression($att->value," as ");
		$itname= "\$__tal_".$name;


		$code .= " $itname = ".($mch[0][0]=="$" && $mch[0][strlen($mch[0])-1]!=")"?"&".$mch[0]:$mch[0])."; \n ";
		$code .= " if ( is_array($itname) || ( $itname instanceof Traversable ) ) {\n";
		
		if($loopName){
			$code .= " \$__foreach[$loopName]=null;\$__foreach[$loopName]=new \\".__NAMESPACE__."\\Attribute_foreach_helper_loop(); \n";
			$code .= " \$__foreach[$loopName]->total=(($itname instanceof Countable) || is_array($itname))?count($itname):NULL;\n";
		}
		$code .= " foreach ( $itname as $mch[1]) { \n";
		
		if($loopName){
			$code .= "\tif(\$__foreach_loop = \$__foreach[$loopName]){\n";
			$code .= "\t\t\$__foreach_loop->index++;\n";
			$code .= "\t\t\$__foreach_loop->counter=(\$__foreach_loop->index-1);\n";
			$code .= "\t\t\$__foreach_loop->odd=(\$__foreach_loop->index%2==0);\n";
			$code .= "\t\t\$__foreach_loop->even= (!\$__foreach_loop->odd);\n";
			$code .= "\t\t\$__foreach_loop->first=( \$__foreach_loop->index==1 );\n";
			$code .= "\t\t\$__foreach_loop->last= ( \$__foreach_loop->index === \$__foreach_loop->total);\n";
			$code .= "\t}\n ";
		}

		$pi = $this->dom->createProcessingInstruction("php",$code);
		$node->parentNode->insertBefore($pi,$node);
		
		$codeEnd = " } //endforeach \n } //endif\n unset($itname";
		if($loopName){
			$codeEnd .=",\$__foreach[$loopName]";
		}
		$codeEnd .= " )";
		
		$pi = $this->dom->createProcessingInstruction("php",$codeEnd);
		$node->parentNode->insertAfter($pi,$node);

	}

}
class Attribute_foreach_helper implements \ArrayAccess, \Countable , \IteratorAggregate  {
	protected $data = array();

	public function count() {
		return count($this->data);
	}
	public function getIterator() {
		return new \ArrayIterator($this->data);
	}
	function offsetExists ($offset){
		return isset($this->data[$offset]);
	}
 	function offsetGet ($offset){
		return $this->data[$offset];
	}
 	function offsetSet ($offset, $value){
		$this->data[$offset]=$value;
	}
 	function offsetUnset ($offset){
		unset($this->data[$offset]);
	}
	public function __get($offset) {
		return $this->data[$offset];
	}
}
class Attribute_foreach_helper_loop{
	public $total=0;
	public $index = 0;
	public $counter =1;
	public $odd=true;
	public $even=false;
	public $first = true;
	public $last = false;
}
