<?php
namespace Goetas\ATal;

interface DOMLoader
{

    public function createDOM($html);

    public function dumpDOM(\DOMDocument $dom, $metadata);

    public function collectMetadata(\DOMDocument $dom, $original);
}
