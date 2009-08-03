<?php

class ATal_XMLDomElement extends \goetas\xml\XMLDomElement {
	public function uniqueId() {
		return $this->getAttributeNS(ATal::NS,"id");
	}
	public function cloneNode($deep) {
		$this->setAttributeNS(ATal::NS,"id",uniqid());
		return parent::cloneNode($deep);
	}
}

?>