<?php 
if ( estadmin() ) {
	header("Content-type: text/plain");
	header("Content-Disposition: attachment; filename=".$dbname.date("Ymd").".sql");
	echo gensql();
} else {
	echo "<p>Vous n'&ecirc;tes pas connect&eacute; en tant qu'administrateur.</p>";
}
?>