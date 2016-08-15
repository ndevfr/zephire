<?php
$classe      = !empty($_GET['idcl']) ? $_GET['idcl'] : NULL;
$classe      = verifclasse($link->real_escape_string($classe));
$discipline  = !empty($_GET['idds']) ? $_GET['idds'] : NULL;
$discipline  = verifdiscipline($link->real_escape_string($discipline));
$chapitre    = !empty($_GET['idch']) ? $_GET['idch'] : NULL;
$chapitre    = verifchapitre($link->real_escape_string($chapitre), $classe, $discipline);
$ceinturesnv = $ceintures;
unset($ceinturesnv[0]);
if (!empty($classe)) {
    $result = $link->query("SELECT * FROM " . $prefix . "classes WHERE id = '$classe'");
    $r      = mysqli_fetch_array($result);
    $niveau = $r['niveau'];
    $nomcl  = $r['nom'];
}
if (!empty($chapitre)) {
    $result  = $link->query("SELECT * FROM " . $prefix . "chapitres WHERE id = '$chapitre'");
    $r       = mysqli_fetch_array($result);
    $arrcomp = explode(',', $r['competences']);
    for ($k = 0; $k < sizeof($arrcomp); $k++) {
        $arrcomp[$k] = $niveau . $discipline . $arrcomp[$k];
    }
    $lstcomp     = implode(",", $arrcomp);
    $competences = "'" . str_replace(",", "','", $lstcomp) . "'";
    $titrech     = $r['nom'];
    $compch      = explode(',', $competences);
} else {
    $chapitre = "";
}
echo "<h1>$nomcl : $titrech</h1>";
echo "<h2>$nompage</h2>";
if ((!empty($classe)) && (!empty($discipline)) && (!empty($chapitre))) {
    if (!empty($_POST['submit'])) {
        $lstcompetences = "";
        foreach ($_POST['competences'] as $idcomp) {
            $lstcompetences .= $idcomp . ",";
        }
        $lstcompetences = substr($lstcompetences, 0, -1);
        $sql            = "UPDATE " . $prefix . "chapitres SET competences = \"$lstcompetences\" WHERE id = '$chapitre'";
        $link->query($sql);
        $arrcomp = explode(',', $lstcompetences);
        for ($k = 0; $k < sizeof($arrcomp); $k++) {
            $arrcomp[$k] = $niveau . $discipline . $arrcomp[$k];
        }
        $lstcomp     = implode(",", $arrcomp);
        $competences = "'" . str_replace(",", "','", $lstcomp) . "'";
        $compch      = explode(',', $competences);
    }
    $result = $link->query("SELECT * FROM " . $prefix . "competences WHERE id LIKE '$niveau%' ORDER BY cat ASC, id ASC");
    echo "<form action='selectcompetences.php?idcl=" . $classe . "&amp;idds=" . $discipline . "&amp;idch=" . $chapitre . "' method='POST'><p>Cocher les compétences qui sont abordées dans le chapitre.</p><table class='data large'><thead><tr><th style='width:20px;'></th><th style='width:40px;'>Id</th><th>Nom</th><th style='width:20px;'>So</th><th style='width:20px;'>#</th></tr></thead><tbody>";
    while ($r = mysqli_fetch_array($result)) {
        $idcp    = substr($r['id'], strlen($niveau) + strlen($discipline));
        $soclecp = $r['socle'];
        $nomcp   = stripslashes($r['nom']);
        $ordrecp = $r['cat'];
        echo "<td><input type='checkbox' name='competences[]' value='$idcp'";
        if (in_array("'" . $niveau . $discipline . $idcp . "'", $compch)) {
            echo " checked";
        }
        echo " />";
        echo "</td><td>$idcp</td><td style='text-align:left;'>$nomcp</cp/><td>$soclecp</td><td>$ordrecp</td></tr>";
    }
    echo "</tbody></table>";
    echo "<p><input name='submit' type='submit' value='Modifier' /> <input name='retour' type='button' value='Retour' onclick='window.location.href=\"mdfprogressions.php?idcl=$classe&idds=$discipline\"' /></p></form>";
}
?>