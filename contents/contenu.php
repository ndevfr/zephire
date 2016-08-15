<?php
session_set_cookie_params(0);
session_start();

if(is_dir("contents")){
	$dir = "contents/";
}else{
	$dir = "../contents/";
}

include($dir . "include.php");

include($dir . "page.php");

if (($inclpage !== "exportcsv.php") && ($inclpage !== "backup.php")) {
    include($dir . "header.php");
}

include($dir . $inclpage);

if (($inclpage !== "exportcsv.php") && ($inclpage !== "backup.php")) {
    include($dir . "footer.php");
}
?>