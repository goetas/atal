<?php 
namespace goetas\atal;
interface IFinder{
	/**
	 * @param string $name
	 * @reutrn Template
	 */
	public function getTemplate(TemplateRef $templateRef);
	/** 
	 * @param TemplateRef $name
	 * @reutrn string
	 */
	public function getCacheName(TemplateRef $templateRef);
	/**
	 * 
	 * @param TemplateRef $template
	 * @param int $current
	 * @return bool
	 */
	public function isFresh(TemplateRef $templateRef, $current);
}