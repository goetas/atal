<?php
namespace goetas\atal\xml;
class XMLDom extends \goetas\xml\XMLDom {
	public function __construct($version = '1.0', $enc = 'UTF-8') {
		parent::__construct( $version, $enc );
		$this->registerNodeClass( 'DOMElement', __NAMESPACE__.'\\XMLDomElement' );
	}
}
