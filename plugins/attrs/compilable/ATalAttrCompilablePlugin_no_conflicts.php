<?php
class ATalAttrCompilablePlugin_no_conflicts extends ATalAttrCompilablePlugin{

	protected $oldRegex = array();
	function start(ATal_XMLDomElement $node, $attValue){

		list($p1, $p2) = explode("|",$attValue);


		array_push($this->oldRegex , $this->compiler->currRegex);

		$this->compiler->currRegex = "/".preg_quote($p1, "/").'([\'a-z\$\\\\].*?)'.preg_quote($p2, "/")."/";



	}
	public function end(ATal_XMLDomElement $node, $attValue) {
		$this->compiler->currRegex = array_pop( $this->oldRegex ) ;
	}

}