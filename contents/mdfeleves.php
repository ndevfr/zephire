<?php
echo "<h1>Modifier les &eacute;l&egrave;ves</h1>";
if ((estprof())) {
    $classe = !empty($_GET['idcl']) ? $_GET['idcl'] : NULL;
    $classe = verifclasse($link->real_escape_string($classe));
    echo "<div class='selecteurs'><p>";
    selectclasse("\"mdfeleves.php?idcl=\" + this.value");
    echo "</p></div>";
    if ((!empty($_POST['subSuppr'])) && (!empty($_POST['supprel']))) {
        $supprel = $link->real_escape_string($_POST['supprel']);
        $sql     = "DELETE FROM " . $prefix . "eleves WHERE idclasse = '$classe' AND id = $supprel";
        $link->query($sql);
    }
    if (!empty($_POST['submit'])) {
        for ($i = 1; $i <= sizeof($_POST['id']); $i++) {
            $idel            = $link->real_escape_string($_POST['id'][$i]);
            $nomel           = $link->real_escape_string($_POST['nom'][$i]);
            $prenomel        = $link->real_escape_string($_POST['prenom'][$i]);
            $sexeel          = $link->real_escape_string($_POST['sexe'][$i]);
            $datenaissanceel = $link->real_escape_string($_POST['datenaissance'][$i]);
            $regimeel        = $link->real_escape_string($_POST['regime'][$i]);
            $optionsel       = $link->real_escape_string($_POST['options'][$i]);
            $commentairesel  = $link->real_escape_string($_POST['commentaires'][$i]);
            $usernameel      = $link->real_escape_string($_POST['username'][$i]);
            if (empty($usernameel)) {
                $usernameel = $link->real_escape_string(strtolower(substr($prenomel, 0, 1)) . strtolower(str_replace(" ", "_", $nomel)));
            }
            $passwordel = $link->real_escape_string($_POST['password'][$i]);
            if (empty($passwordel)) {
                for ($p = 0; $p <= 3; $p++) {
                    $passwordel .= rand(0, 9);
                }
            }
            $passwordel = $link->real_escape_string(encrypt($passwordel));
            $sql        = "UPDATE " . $prefix . "eleves SET nom = '$nomel', prenom = '$prenomel', sexe = '$sexeel', datenaissance = '$datenaissanceel', regime = '$regimeel', options = '$optionsel', commentaires = '$commentairesel', username = '$usernameel', password = '$passwordel' WHERE id = $idel";
            $link->query($sql);
        }
        if (!empty($_POST['nomnew'])) {
            $nomel           = $link->real_escape_string($_POST['nomnew']);
            $prenomel        = $link->real_escape_string($_POST['prenomnew']);
            $sexeel          = $link->real_escape_string($_POST['sexenew']);
            $datenaissanceel = $link->real_escape_string($_POST['datenaissancenew']);
            $regimeel        = $link->real_escape_string($_POST['regimenew']);
            $optionsel       = $link->real_escape_string($_POST['optionsnew']);
            $commentairesel  = $link->real_escape_string($_POST['commentairesnew']);
            $usernameel      = $link->real_escape_string($_POST['usernamenew']);
            if (empty($usernameel)) {
                $usernameel = $link->real_escape_string(strtolower(substr($prenomel, 0, 1)) . strtolower(str_replace(" ", "_", $nomel)));
            }
            $passwordel = $link->real_escape_string($_POST['passwordnew']);
            if (empty($passwordel)) {
                for ($p = 0; $p <= 3; $p++) {
                    $passwordel .= rand(0, 9);
                }
            }
            $passwordel = $link->real_escape_string(encrypt($passwordel));
            $sql        = "INSERT INTO " . $prefix . "eleves (nom, prenom, sexe, datenaissance, regime, options, commentaires, idclasse, username, password) VALUES ('$nomel', '$prenomel', '$sexeel', '$datenaissanceel', '$regimeel', '$optionsel', '$commentairesel', '$classe', '$usernameel', '$passwordel')";
            $link->query($sql);
        }
    }
    if ((!empty($_POST['subUpload'])) && (!empty($_FILES['fichiercsv']['tmp_name']))) {
        $sql = "DELETE FROM " . $prefix . "eleves WHERE idclasse = $classe";
        $link->query($sql);
        $fichiercsv = $_FILES['fichiercsv']['tmp_name'];
        $fic        = fopen("$fichiercsv", 'rb');
        for ($ligne = fgetcsv($fic, 1024, ";"); !feof($fic); $ligne = fgetcsv($fic, 1024, ";")) {
            $symbole = substr($ligne[0], 0, 1);
            if ($symbole !== "#") {
                if (empty($ligne[8])) {
                    for ($p = 0; $p <= 3; $p++) {
                        $ligne[8] .= rand(0, 9);
                    }
                }
                $ligne[0] = $link->real_escape_string($ligne[0]);
                $ligne[1] = $link->real_escape_string($ligne[1]);
                $ligne[2] = $link->real_escape_string($ligne[2]);
                $ligne[3] = $link->real_escape_string($ligne[3]);
                $ligne[4] = $link->real_escape_string($ligne[4]);
                $ligne[5] = $link->real_escape_string($ligne[5]);
                $ligne[6] = $link->real_escape_string($ligne[6]);
                $ligne[7] = $link->real_escape_string($ligne[7]);
                $ligne[8] = $link->real_escape_string(encrypt($ligne[8]));
                $sql      = "INSERT INTO " . $prefix . "eleves (idclasse, nom, prenom, sexe, datenaissance, regime, options, commentaires, username, password, evaluations) VALUES (" . $classe . ", '" . $ligne[0] . "', '" . $ligne[1] . "', '" . $ligne[2] . "', '" . $ligne[3] . "', '" . $ligne[4] . "', '" . $ligne[5] . "', '" . $ligne[6] . "', '" . $ligne[7] . "', '" . $ligne[8] . "', '" . $ligne[9] . "')";
                $link->query($sql);
            }
        }
        fclose($fic);
    }
    if (!empty($classe)) {
        echo "<p>" . effectifcl($classe) . " élève(s)</p>";
        echo "<form action='mdfeleves.php?idcl=" . $classe . "' method='POST'><table class='data large'>";
        echo "<thead><tr><th colspan='4'>Identit&eacute;</th><th>Options</th><th>Commentaires</th></tr></thead><tbody>";
        $result = $link->query("SELECT * FROM " . $prefix . "eleves WHERE idclasse = '$classe' ORDER BY nom ASC, prenom ASC");
        $k      = 1;
        while ($r = mysqli_fetch_array($result)) {
            $idel            = $r['id'];
            $nomel           = $r['nom'];
            $prenomel        = $r['prenom'];
            $sexeel          = $r['sexe'];
            $datenaissanceel = $r['datenaissance'];
            $regimeel        = $r['regime'];
            $optionsel       = $r['options'];
            $commentairesel  = $r['commentaires'];
            $usernameel      = $r['username'];
            $passwordel      = decrypt($r['password']);
            echo "<tr><td style='width:70px;'>Nom :</td><td style='width:140px;'><input type='hidden' name='id[$k]' value='$idel' /><input tabindex=" . $k . "1 type='text' class='inputcell' name='nom[$k]' value='$nomel' placeholder='Nom' /></td><td style='width:95px;'>Pr&eacute;nom :</td><td style='width:150px;'><input tabindex=" . $k . "2 type='text' class='inputcell' name='prenom[$k]' value='$prenomel' placeholder='Prénom' /><td rowspan='4' style='width:200px;'><textarea tabindex=" . $k . "8 placeholder='Options' class='inputcell' rows='6' name='options[$k]'>" . $optionsel . "</textarea></td><td rowspan='4'><textarea tabindex=" . $k . "9 placeholder='Commentaires' class='inputcell' rows='6' name='commentaires[$k]'>$commentairesel</textarea></td></tr>";
            echo "<tr><td>Naissance :</td><td><input tabindex=" . $k . "3 type='text' class='inputcell' name='datenaissance[$k]' value='$datenaissanceel' placeholder='../../....' /></td><td>Sexe :</td><td>";
            echo "<select tabindex=" . $k . "4 name='sexe[$k]' style='width:100%;'>";
            echo "<option value='M' ";
            if ($sexeel == "M") {
                echo "selected";
            }
            echo ">Masculin</option>";
            echo "<option value='F' ";
            if ($sexeel == "F") {
                echo "selected";
            }
            echo ">F&eacute;minin</option>";
            echo "</select></td></tr>";
            echo "<tr><td>R&eacute;gime :</td><td>";
            echo "<select tabindex=" . $k . "5 name='regime[$k]' style='width:100%;'>";
            echo "<option value='Demi-pensionnaire' ";
            if ($regimeel == "Demi-pensionnaire") {
                echo "selected";
            }
            echo ">Demi-pensionnaire</option>";
            echo "<option value='Externe' ";
            if ($regimeel == "Externe") {
                echo "selected";
            }
            echo ">Externe</option>";
            echo "<option value='Interne' ";
            if ($regimeel == "Interne") {
                echo "selected";
            }
            echo ">Interne</option>";
            echo "</select></td><td colspan='2'></td></tr>";
            echo "<tr style='border-bottom:solid 2px;'><td>Identifiant :</td><td><input tabindex=" . $k . "6 type='text' class='inputcell' name='username[$k]' value='$usernameel' placeholder='Identifiant' /></td><td>Mot de passe :</td><td><input tabindex=" . $k . "7 type='text' class='inputcell' name='password[$k]' value='$passwordel' placeholder='Mot de passe' /></td></tr>";
            $k++;
        }
        echo "<tr><td>Nom :</td><td><input tabindex=1 type='text' class='inputcell' name='nomnew' placeholder='Nom' /></td><td>Pr&eacute;nom :</td><td><input tabindex=2 type='text' class='inputcell' name='prenomnew' placeholder='Prénom' /><td rowspan='4'><textarea tabindex=8 placeholder='Options' class='inputcell' rows='6' name='optionsnew'></textarea></td><td rowspan='4'><textarea tabindex=9 placeholder='Commentaires' class='inputcell' rows='6' name='commentairesnew'></textarea></td></tr>";
        echo "<tr><td>Naissance :</td><td><input tabindex=3 type='text' class='inputcell' name='datenaissancenew' placeholder='../../....' /></td><td>Sexe :</td><td>";
        echo "<select tabindex=4 name='sexenew' style='width:100%;'>";
        echo "<option value='M'>Masculin</option>";
        echo "<option value='F'>F&eacute;minin</option>";
        echo "</select></td></tr>";
        echo "<tr><td>R&eacute;gime :</td><td>";
        echo "<select tabindex=5 name='regimenew' style='width:100%;'>";
        echo "<option value='Demi-pensionnaire'>Demi-pensionnaire</option>";
        echo "<option value='Externe'>Externe</option>";
        echo "<option value='Interne'>Interne</option>";
        echo "</select></td><td colspan='2'></td></tr>";
        echo "<tr><td>Identifiant :</td><td><input tabindex=6 type='text' class='inputcell' name='usernamenew' placeholder='Identifiant' /></td><td>Mot de passe :</td><td><input tabindex=7 type='text' class='inputcell' name='passwordnew' placeholder='Mot de passe' /></td></tr>";
        echo "</tbody></table>";
        echo "<p class='noprint'><input type='submit' name='submit' value='Valider' /></p></form>";
        $result = $link->query("SELECT * FROM " . $prefix . "eleves WHERE idclasse = '$classe' ORDER BY nom ASC, prenom ASC");
        echo "<form action='mdfeleves.php?idcl=" . $classe . "' name='formsuppr' method='POST' class='noprint'><p>Supprimer un &eacute;l&egrave;ve : <select name='supprel'><option value='' selected>...</option>";
        while ($r = mysqli_fetch_array($result)) {
            $idel     = $r['id'];
            $nomel    = $r['nom'];
            $prenomel = $r['prenom'];
            echo "<option value='$idel'>$nomel $prenomel</option>";
        }
        echo "</select><input type='submit' value='Valider' name='subSuppr' /></p></form>";
        echo "<h2>Importation et exportation</h2>";
        echo "<form action='mdfeleves.php?idcl=" . $classe . "' name='formupload' enctype='multipart/form-data' method='POST' class='noprint'><p>Importer un fichier CSV : <input type='file' name='fichiercsv' />";
        echo "<input type='submit' value='Valider' name='subUpload' /></p></form>";
        echo "<p class='noprint'>Exporter un fichier CSV : <input type='submit' value='Valider' onclick='location.href=\"exportcsv.php?cont=eleves&amp;idcl=" . $classe . "\"' name='subExport' /></p>";
    } else {
        echo "<p>Vous n'avez pas choisi la classe.</p>";
    }
} else {
    echo "<p>Vous n'&ecirc;tes pas connect&eacute;.</p>";
}
?>