<?php
namespace goetas\atal\plugins\attributes\compilable;
use goetas\atal\xml;
use goetas\atal\CompilableAttribute;
class CompilableAttribute_no_conflicts extends CompilableAttribute{

	protected $oldRegex = array();
	function start(xml\XMLDomElement $node, \DOMAttr $att){

		list($p1, $p2) = explode("|",$att->value);


		array_push($this->oldRegex , $this->compiler->currRegex);

		$this->compiler->currRegex = "/".preg_quote($p1, "/").'([\'a-z\$\\\\].*?)'.preg_quote($p2, "/")."/";



	}
	public function end(xml\XMLDomElement $node, $att) {
		$this->compiler->currRegex = array_pop( $this->oldRegex ) ;
	}

}