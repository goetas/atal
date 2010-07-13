<?php
class ATalAttrCompilablePlugin_extends extends ATalAttrCompilablePlugin{
	function start(ATal_XMLDomElement $node, $attValue){
		$olddir = getcwd();
		$dir = dirname($this->compiler->getTemplate());
		chdir($dir);

		list ($tpl,$tipo,$query ) = $this->compiler->parseUriParts($attValue);
		list($xmlTemplate,$tplDom) =  $this->compiler->loadXMLTemplate($tpl,$tipo,$query );
		chdir($olddir);


		//$xmlTemplate continene il template originale
		//$dom contiene solo i dettagli di estensione per $xmlTemplate
		//$padre è un rifferimento al padre del tag che contine l'attributo "extends"

		$padre = $node->parentNode;
		$dom = $node->ownerDocument;

		$padre->removeChild($node);
		$a =  array("t" => ATal::NS ) ;
		// soltituisco il tag "extends" con il contenuto del template a cui si rifferisce
		foreach ( $xmlTemplate->query( "/t:atal-content/node()|/text()",$a) as $nodeNew ){
			$padre->appendChild($dom->importNode($nodeNew,true));
		}




		// cerco eventuali template dettagliati da sostituire a quelli di default
		foreach ( $node->query( "t:block", $a ) as $templateDettagliato ){
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
					$this->compiler->applyTemplats($nodoDaSostituire->parentNode);
				}else{
					$nodoDaSostituire->removeChilds();
					foreach ($templateDettagliato->childNodes as $n){
						$nd  = $dom->importNode($n->cloneNode(1), true);
						$nodoDaSostituire->appendChild($nd);
					}
					$this->compiler->applyTemplats($nodoDaSostituire);
				}

			}
		}


		return self::STOP_NODE | self::STOP_ATTRIBUTE;
	}


}
