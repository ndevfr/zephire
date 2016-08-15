<?php
echo "<h1 class='noprint'>Liste des identifiants</h1>";
if ((estprof())) {
    $classe = !empty($_GET['idcl']) ? $_GET['idcl'] : NULL;
    $classe = verifclasse($link->real_escape_string($classe));
    
    echo "<div class='selecteurs'><p>";
    selectclasse("\"lstidentifiants.php?idcl=\" + this.value");
    echo "</p></div>";
    if (!empty($classe)) {
        echo "<div class=noprint>";
        //LISTE
        $result = $link->query("SELECT * FROM " . $prefix . "eleves WHERE idclasse = '$classe' ORDER BY nom ASC, prenom ASC");
        $i      = 0;
        echo "<div style='width:100%;overflow:scroll;'><table class='data'><thead><tr><th>El&egrave;ve</th><th>Nom d'utilisateur</th><th>Mot de passe</th></tr></thead><tbody>";
        while ($r = mysqli_fetch_array($result)) {
            $i++;
            $nomel      = $r['nom'];
            $prenomel   = $r['prenom'];
            $usernameel = $r['username'];
            $passwordel = decrypt($r['password']);
            echo "<tr>";
            echo "<td style='text-align:left;'>$prenomel $nomel</td>";
            echo "<td>$usernameel</td>";
            echo "<td>$passwordel</td>";
            echo "</tr>";
        }
        echo "</tbody></table></div>";
        echo "</div>";
        echo "<div class=toprint>";
        //ETIQUETTES
        $result = $link->query("SELECT * FROM " . $prefix . "eleves WHERE idclasse = '$classe' ORDER BY nom ASC, prenom ASC");
        $i      = 0;
        echo "<br /><div style='display:block;width:16.62cm;'>";
        $num = $result->num_rows;
        while ($r = mysqli_fetch_array($result)) {
            $nomel      = $r['nom'];
            $prenomel   = $r['prenom'];
            $usernameel = $r['username'];
            $passwordel = decrypt($r['password']);
            if ((($i == 0) || ($i == $num - 1)) && (fmod($i, 2) == 0)) {
                echo "<div class='etiquette' style='float:left;'>";
            } elseif (fmod($i, 2) == 0) {
                echo "<div class='etiquette' style='display:block;float:left;page-break-inside:avoid;'>";
            } else {
                echo "<div class='etiquette' style='display:block;float:right;'>";
            }
            echo "<div style='text-align:center;'><b>.:. Evaluations en Math&eacute;matiques .:.</b></div>";
            echo "Retrouvez le détail de vos évaluations (barême, compétences acquises, points obtenus, etc.) à l'adresse :<br />";
            echo "<b>http://m.desmarets.eu/evaluations/</b><br />";
            echo "$prenomel $nomel<br />";
            echo "Nom d'utilisateur : <b>$usernameel</b><br />";
            echo "Mot de passe : <b>$passwordel</b>";
            echo "</div>";
            $i++;
        }
        echo "</div>";
        echo "</div>";
    } else {
        echo "<p>Vous n'avez pas choisi la classe.</p>";
    }
} else {
    echo "<p>Vous n'&ecirc;tes pas connect&eacute;.</p>";
}
?>