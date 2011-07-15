<?php
class ATalAttrCompilablePlugin_extends extends ATalAttrCompilablePlugin{
	function start(ATal_XMLDomElement $node, $attValue){

		$dir = dirname($this->compiler->getTemplate());

		$this->scanTemplates($dir, $node, $attValue);

		return self::STOP_NODE | self::STOP_ATTRIBUTE;
	}
	protected function scanTemplates($dir , ATal_XMLDomElement $node, $attValue, &$blocksNodes = array()){
		$a =  array("t" => ATal::NS ) ;

		$olddir = getcwd();
		chdir($dir);
		list ($tpl,$tipo,$query ) = $this->compiler->parseUriParts($attValue);
		list($xmlTemplate,$tplDom) =  $this->compiler->loadXMLTemplate($tpl,$tipo,$query );



		//$xmlTemplate continene il template originale
		//$dom contiene solo i dettagli di estensione per $xmlTemplate
		//$padre è un rifferimento al padre del tag che contine l'attributo "extends"

		$padre = $node->parentNode;
		$dom = $node->ownerDocument;
		//echo htmlspecialchars($xmlTemplate->saveXML()),"<hr/>";

		foreach ( $node->query( "t:block", $a ) as $templateDettagliato ){
			$name = $templateDettagliato->getAttribute("name");
			$blocksNodes[$name] = $templateDettagliato;
		}

		$padre->removeChild($node);

		// soltituisco il tag "extends" con il contenuto del template a cui si rifferisce
		foreach ( $xmlTemplate->query( "/t:atal-content/node()|/text()",$a) as $nodeNew ){
			$nd = $dom->importNode($nodeNew,true);
			$padre->appendChild($nd);
			if($nd instanceof ATal_XMLDomElement && $nd->getAttributeNS(ATal::NS, 'extends')){
				$this->scanTemplates(dirname($tpl) , $nd, $nd->getAttributeNS(ATal::NS, 'extends'), $blocksNodes );
			}
		}
		chdir($olddir);

		// cerco eventuali template dettagliati da sostituire a quelli di default

		foreach ( $blocksNodes as $templateDettagliato ){
			$nomeTemplate = $templateDettagliato->getAttribute("name");
			$sostituisci = $templateDettagliato->getAttribute("replace")=="true";

			$nodiDaSostituire = $dom->query( "//*[@t:block='$nomeTemplate']", $a );

			foreach($nodiDaSostituire as $nodoDaSostituire){
				if($sostituisci){
					$p = $nodoDaSostituire->parentNode;
					$ref = $nodoDaSostituire;
					foreach ($templateDettagliato->childNodes as $n){
						$nd  = $dom->importNode($n->cloneNode(1), true);
						$p->insertAfter($nd, $ref);
						$ref = $nd;
					}
					$nodoDaSostituire->parentNode->removeChild($nodoDaSostituire);
				}else{
					$nodoDaSostituire->removeChilds();
					foreach ($templateDettagliato->childNodes as $n){
						$nd  = $dom->importNode($n->cloneNode(1), true);
						$nodoDaSostituire->appendChild($nd);
					}
				}

			}
		}

		$this->compiler->applyTemplats($padre);
	}


}