<?php
spl_autoload_register (function($cname){
	$ns = "goetas\\atal\\";
	if(strpos($cname,$ns)!==false){
		//echo $cname." autoload<hr/>";
		$path = __DIR__.DIRECTORY_SEPARATOR.str_replace("\\",DIRECTORY_SEPARATOR,substr($cname,strlen($ns))).".php";
		if(is_file($path)){
			require_once($path);
		}
	}
}); 
?>