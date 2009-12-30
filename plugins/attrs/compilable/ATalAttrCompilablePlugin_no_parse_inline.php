<?php
class ATalAttrCompilablePlugin_no_parse_inline extends ATalAttrCompilablePlugin {

	public function init() {
		$this->compiler->addPostFilter( array(__CLASS__, "replaceInlines" ) );
	}
	function start(ATal_XMLDomElement $node, $attValue) {
		foreach ( $node->query( ".//text()|.//@*", array("t" => ATal::NS ) ) as $nodo ){
			if($nodo instanceof DOMAttr){
				$nodo->value = preg_replace(ATalCompiler::VAR_REGEX,"__atal_inline\\1}atal_inline__",$nodo->value);
			}elseif ($nodo instanceof DOMText){
				$nodo->data = preg_replace(ATalCompiler::VAR_REGEX,"__atal_inline\\1}atal_inline__",$nodo->data);
			}
		}
		return self::STOP_NODE;
	}
	public static function replaceInlines($stream) {
		return preg_replace( "/__atal_inline([^\\}]+)\\}atal_inline__/", "{\\1}", $stream );
	}

}