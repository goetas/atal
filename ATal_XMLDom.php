<?php

class ATal_XMLDom extends XMLDom {
	public function __construct($version = '1.0', $enc = 'UTF-8') {
		parent::__construct( $version, $enc );
		$this->registerNodeClass( 'DOMElement', 'ATal_XMLDomElement' );
	}
}
?>