<?php 

require __DIR__."/../vendors/pluginsys/src/autoload.php";
require __DIR__."/../src/autoload.php";



//psr-0 autoloader
foreach(array(
		"goetas\\xml\\"=>__DIR__."/../vendors/xmldom/src/",
		//"goetas\\atal\\"=>__DIR__."/../src/",
		) as $ns => $dir){

	spl_autoload_register ( function($cname)use($ns, $dir){
		if(strpos($cname,$ns)===0){
			$path = $dir.strtr($cname, "\\","/").".php";
			require_once ($path);
		}
	});
}

	
