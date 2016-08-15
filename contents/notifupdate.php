<?php
$url         = "https://raw.github.com/ndesmarets/zephire/master/VERSION";
$lastversapp = getSslPage($url);
if ($lastversapp == false) {
    $notifupd = "<img src='../contents/images/Update/warning.png' alt='ERREUR!' style='vertical-align:bottom;' /> Votre h&eacute;bergeur bloque tous les acc&egrave;s aux sites distants. Il est impossible de v&eacute;rifier si vous disposez de la derni&egrave;re version de l'application.<br />Vous pouvez le v&eacute;rifier par vous-m&ecirc;me en vous rendant sur la page <a href='http://www.desmarets.eu/zephire/' target='_blank'>http://www.desmarets.eu/zephire/</a>.";
} elseif ($lastversapp > $versapp) {
    $notifupd = "<img src='../contents/images/Update/nolast.png' alt='NON!' style='vertical-align:bottom;' /> Une version plus r&eacute;cente de l'application est disponible sur le site <a href='http://www.desmarets.eu/zephire/' target='_blank'>http://www.desmarets.eu/zephire/</a>.";
} else {
    $notifupd = "<img src='../contents/images/Update/oklast.png' alt='OK!' style='vertical-align:bottom;' /> Vous disposez de la version la plus r&eacute;cente de l'application.";
}
echo "<h1>Mises &agrave; jour</h1>";
echo "<p>" . $notifupd . "</p>";
?>