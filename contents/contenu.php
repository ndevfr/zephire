<?php
session_set_cookie_params(0);
session_start();

include("../contents/include.php");

include("../contents/page.php");

if (($inclpage !== "exportcsv.php") && ($inclpage !== "backup.php")) {
    include("../contents/header.php");
}

include("../contents/" . $inclpage);

if (($inclpage !== "exportcsv.php") && ($inclpage !== "backup.php")) {
    include("../contents/footer.php");
}
?>