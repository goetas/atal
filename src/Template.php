<?php
namespace goetas\atal;
class Template {
	/**
	 * @var TemplateRef
	 */
	protected $ref;
	/**
	 * @var string
	 */
	protected $fullName;
	protected $content;
	
	public function __construct(TemplateRef $ref, IFinder $finder) {
		$this->ref = $ref;
		$this->finder = $finder;
	
	}
	
	/**
	 * 
	 * @var IFinder
	 */
	protected $finder;
	
	/**
	 * @param field_type $content
	 */
	public function setContent($content) {
		$this->content = $content;
	}

	/**
	 * @return the $finder
	 */
	public function getFinder() {
		return $this->finder;
	}

	/**
	 * @return the $ref
	 */
	public function getRef() {
		return $this->ref;
	}	
	/**
	 * @return the $fullName
	 */
	public function getFullName() {
		return $this->fullName;
	}
	/**
	 * @return the $content
	 */
	public function getContent() {
		return $this->content;
	}
	/**
	 * @param field_type $fullName
	 */
	public function setFullName($fullName) {
		$this->fullName = $fullName;
	}
}
