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
namespace goetas\atal\plugins\attributes\compilable;
use goetas\atal\xml;
use goetas\atal\CompilableAttribute;
class CompilableAttribute_translate_attr extends CompilableAttribute {
	protected $attrs = array();
	public function init() {
		$this->compiler->addPostFilter( array(__CLASS__, "replaceAttrs" ) );
		$this->compiler->addPreWriteFilter( array($this, "removeAttrs" ) );
	}
	public static function replaceAttrs($stream) {
		return preg_replace( "/" . preg_quote( 'atal-attr="__atal-attr($', '/' ) . '([A-Za-z0-9_]+)' . preg_quote( ')"', '/' ) . '/', "<?php foreach (\$\\1 as \$__attName => \$__attValue){" . " echo \$__attName.\"=\\\"\$__attValue\\\" \";" . " } \n unset(\$\\1, \$__attName,\$__attValue); ?>", $stream );
	}
	public function removeAttrs() {
		foreach ( $this->attrs as $attrData ){
			$attrData [0]->removeAttribute( $attrData [1] );
		}
	}
	function start(xml\XMLDomElement $node, \DOMAttr $att) {
		$parts = $this->compiler->splitExpression( $att->value, ";" );
		$attrs = array();

		$examine = $node;
		$domain = "null";
		do{
			if(is_array($examine->attributes)){
				foreach ($examine->attributes as $attr) {
					if($attr->namespaceURI==ATal::NS && $attr->locanName=='translate-domain'){
						$domain = "'".addcslashes($attr->value,"\\'")."'";
						break 2;
					}
				}
			}
			$examine = $examine->parentNode;
		}while($examine);

		foreach ( $parts as $part ){
			$mch = array();
			if(preg_match( "/^([a-z:_\\-]+)\\s*\\((.+)/i", $part, $mch )){
				$mch [2] = trim( $mch [2] );
				if($mch [2] [strlen( $mch [2] ) - 1] != ')'){
					throw new Exception( "errore di sintassi vicino a '" . $part . "'" );
				}
				$attrs [$mch [1]] = substr(trim( $mch [2] ), 0, -1);
			}else{
				$attrs [$part] = '';
			}
		}
		$varName = "\$__attr_" . $node->uniqueId();

		$code = $varName . " = (array)$varName;\n";

		foreach ( $attrs as $attName => $attParams ){

			$params = array();
			foreach ( $this->compiler->splitExpression( $attParams, ";" ) as $part ){
				list ( $k, $v ) = $this->compiler->splitExpression( $part, "=" );
				if(strlen( $v )){
					if ($v[0]=='(' && $v[strlen($v)-1]== ')'){
						$v = substr($v, 1, -1);
					}
					$params [$k] = $this->compiler->parsedExpression( $v );

				}else{
					if ($k[0]=='(' && $k[strlen($k)-1]== ')'){
						$k = substr($k, 1, -1);
					}
					$params [] = $this->compiler->parsedExpression( $k );
				}
			}

			$code .= $varName . "['$attName']=htmlspecialchars(" . __CLASS__ . "::translate('" . addcslashes( $node->getAttribute( $attName ), "'\\" ) . "',array(" . ATalCompiler::implodeKeyed( $params ) . "), $domain ),ENT_NOQUOTES,'UTF-8');\n";
			$this->attrs [] = array($node, $attName );
		}
		$pi = $this->dom->createProcessingInstruction( "php", $code );
		$node->parentNode->insertBefore( $pi, $node );
		$node->setAttribute( "atal-attr", "__atal-attr($varName)" );
	}

	public static function translate($str, array $params, $domain) {
		return \ambient\i18n\I18nClass::t( $str, $params, $domain );
	}

}