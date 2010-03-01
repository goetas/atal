<?php
/**
 * Traduce gli attributi di un elemento
 * ES:
 * &lt;img xmlns:t="ATal" title="eventi" t:translate-attr="title"/&gt;
 * si possono tradurre più attributi allo stesso tempo e si possono specificare più variabili per ogni attributo
 * ES:
 * &lt;img xmlns:t="ATal" alt="eventi del %periodo" title="eventi dell'%anno" t:translate-attr="title(anno='2009';mese='10');alt(periodo='10-2009')"/&gt;
 * si possono applicare dei modificatori ai valori delle variabili degli attributi, basta dividere le espressioni con le parentesi tonde
 * ES:
 * &lt;img xmlns:t="ATal" title="eventi dell'%anno" t:translate-attr="title(anno=('2009'|modificatore_generico))"/&gt;
 */

class ATalAttrCompilablePlugin_translate_domain extends ATalAttrCompilablePlugin {
	function start(ATal_XMLDomElement $node, $attValue) {

	}
}