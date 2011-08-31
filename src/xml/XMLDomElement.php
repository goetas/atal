<?php
namespace goetas\atal\xml;
class XMLDomElement extends \goetas\xml\XMLDomElement {
	public function uniqueId() {
		return $this->getAttributeNS(\goetas\atal\ATal::NS,"id");
	}
	public function cloneNode($deep) {
		$this->setAttributeNS(\goetas\atal\ATal::NS,"id",uniqid());
		return parent::cloneNode($deep);
	}
}
