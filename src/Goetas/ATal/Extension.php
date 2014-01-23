<?php
namespace Goetas\ATal;

interface Extension
{

    public function getAttributes();

    public function getNodes();

    public function getPostFilters();

    public function getPreFilters();

    public function getDOMLoaders();
}
