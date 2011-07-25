<?php 
// percorso del checkout di atal
require "atal/autoload.php";
require "pluginsys/autoload.php";
require "xmldom/autoload.php";

$tal = new \goetas\atal\ATalXHTML();

$tal->setCompileDir('.');

$tal->variabile =  "hello";

$tal->output("template.html");