<?php
echo "<h1>Progressions</h1>";
$classe     = !empty($_GET['idcl']) ? $_GET['idcl'] : NULL;
$classe     = verifclasse($link->real_escape_string($classe));
$discipline = !empty($_GET['idds']) ? $_GET['idds'] : NULL;
$discipline = verifdiscipline($link->real_escape_string($discipline));
if (!empty($classe)) {
    $infocl    = infocl($classe);
    $niveau    = $infocl['niveau'];
    $nomclasse = $infocl['nom'];
}
echo "<div class='selecteurs'><p>";
selectclasse("\"progressions.php?idcl=\" + this.value", true);
selectdiscipline("\"progressions.php?idcl=$classe&idds=\" + this.value", true);
echo "</p></div>";
if ((!empty($classe)) && (!empty($discipline))) {
    $result = $link->query("SELECT * FROM " . $prefix . "chapitres WHERE id LIKE '$classe%' ORDER BY id ASC");
    while ($r = mysqli_fetch_array($result)) {
        $idchapitre  = $r['id'];
        $nomchapitre = $r['nom'];
        echo "<p><strong>" . substr($idchapitre, strlen($classe) + strlen($discipline)) . ") " . $nomchapitre . "</strong></p>";
        $infoch      = infoch($idchapitre);
        $competences = $infoch["competences"];
        $resultc     = $link->query("SELECT * FROM " . $prefix . "competences WHERE id in ($competences) ORDER BY cat ASC, id ASC");
        while ($r = mysqli_fetch_array($resultc)) {
            echo substr($r['id'], strlen($niveau) + strlen($discipline)) . " : " . stripslashes($r['nom']);
            if (!empty($r['socle'])) {
                echo " [" . $r['socle'] . "]";
            }
            echo "<br>";
        }
    }
} else {
    echo "<p>Vous n'avez pas choisi la classe.</p>";
}
?>