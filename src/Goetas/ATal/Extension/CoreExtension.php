<?php
namespace Goetas\ATal\Extension;

use DOMDocument;
use Goetas\ATal\DOMLoader\XMLDOMLoader;
use Goetas\ATal\DOMLoader\XHTMLDOMLoader;
use Goetas\ATal\Extension;
use Goetas\ATal\Attribute;
use Goetas\ATal\Node;

class CoreExtension implements Extension
{

    const NS = "ATal";

    public function getDOMLoaders()
    {
        return array(
            'xml' => new XMLDOMLoader()
        );
    }

    public function getAttributes()
    {
        $attributes = array();
        $attributes[self::NS]['__base__'] = new Attribute\Attribute_base();
        $attributes[self::NS]['set'] = new Attribute\Attribute_set();
        $attributes[self::NS]['content'] = new Attribute\Attribute_content();
        $attributes[self::NS]['omit'] = new Attribute\Attribute_omit();
        $attributes[self::NS]['capture'] = new Attribute\Attribute_capture();
        $attributes[self::NS]['attr'] = new Attribute\Attribute_attr();
        $attributes[self::NS]['attr-append'] = new Attribute\Attribute_attr_append();
        $attributes[self::NS]['attr-translate'] = new Attribute\Attribute_attr_translate();
        return $attributes;
    }

    public function getNodes()
    {
        $nodes = array();
        $nodes[self::NS]['extends'] = new Node\ExtendsNode();
        $nodes[self::NS]['block'] = new Node\BlockNode();
        $nodes[self::NS]['macro'] = new Node\MacroNode();
        $nodes[self::NS]['import'] = new Node\ImportNode();
        $nodes[self::NS]['include'] = new Node\IncludeNode();
        return $nodes;
    }

    public function getPostFilters()
    {
        return array(
            function ($string)
            {
                return preg_replace('#<(.*) xmlns:[a-zA-Z0-9]+=("|\')' . CoreExtension::NS . '("|\')(.*)>#m', "<\\1\\4>", $string);
            },
            function ($string)
            {
                return preg_replace_callback('/ __attr__="(__a[0-9a-f]+)"/', function ($mch)
                {
                    return '{% for ___ak,____av in ' . $mch[1] . ' %} {{____ak}}="{{ ____av|join(\'\') }}"{% endfor %}';
                }, $string);
            }
        );
    }

    public function getPreFilters()
    {
        return array();
    }
}
