<?php
namespace Goetas\ATal\Extension;

use Goetas\ATal\Extension;
use Goetas\ATal\DOMLoader\HTML5DOMLoader;

class HTML5Extension implements Extension
{

    public function getDOMLoaders()
    {
        return array(
            'html5' => new HTML5DOMLoader()
        );
    }

    public function getAttributes()
    {
        return array();
    }

    public function getNodes()
    {
        return array();
    }

    public function getPostFilters()
    {
        return array();
    }

    public function getPreFilters()
    {
        return array();
    }
}
