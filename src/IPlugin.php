<?php
namespace goetas\atal;
/**
 *
 * Interfaccia di base per tutti i plugin
 * @author goetas
 *
 */
interface IPlugin{
	/**
	 *
	 * Inizializzazione del pliugin
	 * @param array $options
	 * @return void
	 */
	function init(array $options=array());
	/**
	 *
	 * ritorna un array dei plugin da qui dipende questo plugin
	 * @return array
	 */
	public function depends();

	function setATal(ATal $atal);
}
