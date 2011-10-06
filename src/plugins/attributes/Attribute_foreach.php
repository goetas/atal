<?php
namespace goetas\atal\plugins\attributes;
use goetas\atal;
use goetas\xml;
use goetas\atal\Attribute;
use ArrayAccess;
use Countable;
use IteratorAggregate;
use ArrayIterator;
use DOMAttr;
class Attribute_foreach extends Attribute {
	protected $fatto = false;
	public function prependPI() {
		if (! $this->fatto) {
			$str = "\nrequire_once( '" . addslashes ( __FILE__ ) . "');\n";
			$str .= "if(!(\$__foreach instanceof \\" . __NAMESPACE__ . "\\Attribute_foreach_helper)) {\n\t\$__foreach = new \\" . __NAMESPACE__ . "\\Attribute_foreach_helper();\n}";

			$pi = $this->dom->createProcessingInstruction ( "php", $str );
			$this->dom->insertBefore ( $pi, $this->dom->documentElement );
			$this->fatto = true;
		}
	}
	function start(xml\XMLDomElement $node, DOMAttr $att) {
		$this->prependPI ();

		$name = uniqid ( 'l' );
		$loopName = "'default'";
		if (preg_match ( "/^([a-zA-Z_0-9]+)\\s*:\\s*([^:]+)/", $att->value, $mch )) {
			$loopName = "'" . $mch [1] . "'";
			$att->value = trim ( $mch [2] );
		}

		$mch = $this->compiler->splitExpression ( $att->value, " as " );
		$itname = "\$__tal_" . $name;

		$code .= " $itname = " . ($mch [0] [0] == "$" && $mch [0] [strlen ( $mch [0] ) - 1] != ")" ? "&" . $mch [0] : $mch [0]) . "; \n ";
		$code .= " if ( is_array($itname) || ( $itname instanceof Traversable ) ) {\n";

		if ($loopName) {
			$code .= "\t\$__foreach[$loopName]=new \\" . __NAMESPACE__ . "\\Attribute_foreach_helper_loop(); \n";
			$code .= "\t\$__foreach_{$name} = \$__foreach[$loopName];\n";
			$code .= "\t\$__foreach_{$name}->total=(($itname instanceof Countable) || is_array($itname))?count($itname):null;\n";
		} else {
			$code .= "\t\$__foreach_loop{$name} = null;";
		}

		$code .= " foreach ( $itname as $mch[1]) { \n";



		$pi = $this->dom->createProcessingInstruction ( "php", $code );
		$node->parentNode->insertBefore ( $pi, $node );

		$codeEnd = '';
		if ($loopName) {
			$codeEnd .= "\t\$__foreach_loop = &\$__foreach_{$name};\n";
			$codeEnd .= "\t\$__foreach_{$name}->index++;\n";
		}
		$codeEnd .= " } //endforeach \n } //endif\n unset($itname";
		if ($loopName) {
			$codeEnd .= ",\$__foreach[$loopName]";
		}
		$codeEnd .= " )";

		$pi = $this->dom->createProcessingInstruction ( "php", $codeEnd );
		$node->parentNode->insertAfter ( $pi, $node );

	}

}
class Attribute_foreach_helper implements ArrayAccess, Countable, IteratorAggregate {
	protected $data = array ();

	public function count() {
		return count ( $this->data );
	}
	public function getIterator() {
		return new ArrayIterator ( $this->data );
	}
	function offsetExists($offset) {
		return isset ( $this->data [$offset] );
	}
	function offsetGet($offset) {
		return $this->data [$offset];
	}
	function offsetSet($offset, $value) {
		$this->data [$offset] = $value;
	}
	function offsetUnset($offset) {
		unset ( $this->data [$offset] );
	}
	public function __get($offset) {
		return $this->data [$offset];
	}
}
class Attribute_foreach_helper_loop extends atal\BaseClass {
	public $total = 0;
	public $index = 0;
	function &__get($offset) {
		switch ($offset) {
			case "odd" :
				return $this->index % 2 == 0;
				break;
			case "even" :
				return $this->index % 2 != 0;
				break;
			case "first" :
				return $this->index == 0;
				break;
			case "last" :
				return $this->total && $this->index == ($this->total - 1);
				break;
			case "counter" :
				return $this->index + 1;
				break;
			default :
				return parent::__get ( $offset );
				break;
		}
	}
}
