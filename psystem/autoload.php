<?php
spl_autoload_register (function($cname){
	
	$ns = "goetas\\pluginsys\\";
	if(strpos($cname,$ns)!==false){
		//echo $cname." autoload<hr/>";
		$path = __DIR__.DIRECTORY_SEPARATOR.str_replace("\\",DIRECTORY_SEPARATOR,substr($cname,strlen($ns))).".php";
		require_once($path);
	}
}); 
?>