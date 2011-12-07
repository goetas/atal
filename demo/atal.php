<?php 
// percorso del checkout di atal
require __DIR__."/../src/autoload.php";
require __DIR__."/../vendors/pluginsys/src/autoload.php";
require __DIR__."/../vendors/xmldom/src/autoload.php";

$tal = new \goetas\atal\ATalXHTML();
$tal->debug = 1;

$tal->setCompileDir(sys_get_temp_dir());

$tal->users =  array("paul"=>25, "frank"=>65, "mark"=>28, "miller"=>rand(90,100));

// template
$tal->output("template.html");


// extended template
$tal->output("template-ext.html");
