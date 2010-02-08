<?php
class ATalAttrCompilablePlugin_foreach extends ATalAttrCompilablePlugin{
	public function init() {
		$str =" require_once( '" . addslashes( __FILE__ ) . "'); \n";
		$str .=" if(!(\$__foreach instanceof ATalAttrCompilablePlugin_foreach_helper))  \$__foreach = new ATalAttrCompilablePlugin_foreach_helper();\n";
		$pi = $this->dom->createProcessingInstruction( "php", $str );
		$this->dom->insertBefore( $pi, $this->dom->documentElement );

	}
	function start(ATal_XMLDomElement $node, $attValue){

		$name = uniqid('l');
		if(preg_match("/^([a-zA-Z_0-9]+)\s*:\s*([^:]+)/",$attValue,$mch) ){
			$loopName = "'".$mch[1]."'";
			$attValue = trim($mch[2]);
		}else{
			$loopName = "'".$name."'";
		}

		$mch = ATalCompiler::splitExpression($attValue," as ");
		$itname= "\$__tal_".$name;


		$code .= " $itname = ".($mch[0][0]=="$" && $mch[0][strlen($mch[0])-1]!=")"?"&".$mch[0]:$mch[0])."; \n ";
		$code .= " if ( is_array($itname) || ( $itname instanceof Traversable ) ) : ";

		$code .= " \$__foreach[$loopName]=null;\$__foreach[$loopName]=new stdClass(); \n";
		$code .= " \$__foreach[$loopName]->total=(($itname instanceof Countable) || is_array($itname))?count($itname):NULL;\n";

		$code .= " foreach ( $itname as  $mch[1]) : \n";

		$code .= " if(\$__foreach[$loopName]){\n";

		$code .= " \$__foreach[$loopName]->index++;\n";
		$code .= " \$__foreach[$loopName]->counter=(\$__foreach[$loopName]->index-1);\n";
		$code .= " \$__foreach[$loopName]->odd=(\$__foreach[$loopName]->index%2==0);\n";
		$code .= " \$__foreach[$loopName]->even= (!\$__foreach[$loopName]->odd);\n";
		$code .= " \$__foreach[$loopName]->first=( \$__foreach[$loopName]->index==1 );\n";
		$code .= " \$__foreach[$loopName]->last= ( \$__foreach[$loopName]->index === \$__foreach[$loopName]->total);\n";
		$code .= " }\n ";

		$pi = $this->dom->createProcessingInstruction("php",$code);
		$node->parentNode->insertBefore($pi,$node);
		$pi = $this->dom->createProcessingInstruction("php"," endforeach; endif; unset($itname,\$__foreach[$loopName])");
		$node->parentNode->insertAfter($pi,$node);

	}

}
class ATalAttrCompilablePlugin_foreach_helper implements ArrayAccess, Countable , IteratorAggregate  {
	protected $data = array();

	public function count() {
		return count($this->data);
	}
	public function getIterator() {
		return new ArrayIterator($this->data);
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
