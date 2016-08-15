<?php
echo "<h1>Modifier les comp&eacute;tences</h1>";
if (estprof()) {
    $niveau     = !empty($_GET['idnv']) ? $_GET['idnv'] : NULL;
    $niveau     = verifniveau($link->real_escape_string($niveau));
    $discipline = !empty($_GET['idds']) ? $_GET['idds'] : NULL;
    $discipline = verifdiscipline($link->real_escape_string($discipline));
    echo "<div class='selecteurs'><p>";
    selectniveau("\"mdfcompetences.php?idnv=\" + this.value");
    selectdiscipline("\"mdfcompetences.php?idnv=$niveau&idds=\" + this.value");
    echo "</p></div>";
    if ((!empty($_POST['submit']))) {
        for ($i = 1; $i <= sizeof($_POST['id']); $i++) {
            $idcp  = $link->real_escape_string(str_replace(" ", "", $niveau . $discipline . $_POST['id'][$i]));
            $nomcp = $link->real_escape_string($_POST['nom'][$i]);
            $catcp = $link->real_escape_string($_POST['ordre'][$i]);
            $soccp = $link->real_escape_string($_POST['socle'][$i]);
            $sql   = "UPDATE " . $prefix . "competences SET nom = '$nomcp', cat = '$catcp', socle = '$soccp' WHERE id = '$idcp'";
            $link->query($sql);
        }
        if (!empty($_POST['idnew'])) {
            $idcp  = $link->real_escape_string(str_replace(" ", "", $niveau . $discipline . $_POST['idnew']));
            $nomcp = $link->real_escape_string($_POST['nomnew']);
            $catcp = $link->real_escape_string($_POST['ordrenew']);
            $soccp = $link->real_escape_string($_POST['soclenew']);
            $sql   = "INSERT INTO " . $prefix . "competences (id, nom, cat, socle) VALUES ('$idcp', '$nomcp', '$catcp', '$soccp')";
            $link->query($sql);
        }
    }
    if ((!empty($_POST['subSuppr'])) && (!empty($_POST['supprcp']))) {
        $supprcp = $link->real_escape_string($_POST['supprcp']);
        $sql     = "DELETE FROM " . $prefix . "competences WHERE id = '$supprcp'";
        $link->query($sql);
    }
    if ((!empty($_POST['subUpload'])) && (!empty($_FILES['fichiercsv']['tmp_name']))) {
        $sql = "DELETE FROM " . $prefix . "competences WHERE niveau = $niveau";
        $link->query($sql);
        $fichiercsv = $_FILES['fichiercsv']['tmp_name'];
        $fic        = fopen("$fichiercsv", 'rb');
        for ($ligne = fgetcsv($fic, 1024, ";"); !feof($fic); $ligne = fgetcsv($fic, 1024, ";")) {
            $symbole = substr($ligne[0], 0, 1);
            if ($symbole !== "#") {
                $cat   = $link->real_escape_string($ligne[0]);
                $id    = $link->real_escape_string($niveau . $discipline . $ligne[1]);
                $socle = $link->real_escape_string($ligne[2]);
                $nom   = $link->real_escape_string($ligne[3]);
                $sql   = "INSERT INTO " . $prefix . "competences (id, nom, cat, socle) VALUES ('$id', '$nom', $cat, '$socle')";
                $link->query($sql);
            }
        }
        fclose($fic);
    }
    if (!empty($niveau) && !empty($discipline)) {
        echo "<form action='mdfcompetences.php?idnv=" . $niveau . "&idds=" . $discipline . "' method='POST'><table class='data large'><thead><tr><th style='width:40px;'>Id</th><th>Nom</th><th style='width:20px;'>So</th><th style='width:20px;'>#</th></tr></thead><tbody>";
        $result = $link->query("SELECT * FROM " . $prefix . "competences WHERE id LIKE '$niveau$discipline%' ORDER BY cat ASC, id ASC");
        $i      = 1;
        while ($r = mysqli_fetch_array($result)) {
            $idcp    = substr($r['id'], strlen($niveau) + strlen($discipline));
            $soclecp = $r['socle'];
            $nomcp   = stripslashes($r['nom']);
            $ordrecp = $r['cat'];
            echo "<tr>";
            echo "<td style='width:40px;'><input type='text' class='inputcell' value=\"$idcp\" name='id[$i]' /></td>";
            echo "<td><input type='text' class='inputcell' value=\"$nomcp\" name='nom[$i]' /></td>";
            echo "<td style='width:20px;'><input type='text' class='inputcell' value=\"$soclecp\" name='socle[$i]' /></td>";
            echo "<td style='width:20px;'><input type='text' class='inputcell' value=\"$ordrecp\" name='ordre[$i]' /></td>";
            echo "</tr>";
            $i++;
        }
        echo "<tr>";
        echo "<td style='width:40px;'><input type='text' class='inputcell' value='' name='idnew' placeholder='...' /></td>";
        echo "<td><input type='text' class='inputcell' value='' name='nomnew' placeholder='...' /></td>";
        echo "<td style='width:20px;'><input type='text' class='inputcell' value='' name='soclenew' placeholder='...' /></td>";
        echo "<td style='width:20px;'><input type='text' class='inputcell' value='' name='ordrenew' placeholder='...' /></td>";
        echo "</tr></tbody></table>";
        echo "<p class='noprint'><input type='submit' value='Valider' name='submit' /></p></form>";
        $result = $link->query("SELECT * FROM " . $prefix . "competences WHERE id LIKE '$niveau$discipline%' ORDER BY cat ASC, id ASC");
        echo "<form action='mdfcompetences.php?idnv=" . $niveau . "&idds=" . $discipline . "' name='formsuppr' method='POST' class='noprint'><p>Supprimer une comp&eacute;tence : <select name='supprcp'><option value='' selected>...</option>";
        while ($r = mysqli_fetch_array($result)) {
            $idcp  = $r['id'];
            $nomcp = stripslashes($r['nom']);
            echo "<option value='$idcp'>" . substr($idcp, strlen($niveau) + strlen($discipline)) . " : $nomcp</option>";
        }
        echo "</select><input type='submit' value='Valider' name='subSuppr' /></p></form>";
        echo "<h2>Importation et exportation</h2>";
        echo "<form action='mdfcompetences.php?idnv=" . $niveau . "&idds=" . $discipline . "' name='formupload' enctype='multipart/form-data' method='POST' class='noprint'><p>Importer un fichier CSV : <input type='file' name='fichiercsv' />";
        echo "<input type='submit' value='Valider' name='subUpload' /></p></form>";
        echo "<p class='noprint'>Exporter un fichier CSV : <input type='submit' value='Valider' onclick='location.href=\"exportcsv.php?cont=competences&amp;idnv=" . $niveau . "&idds=" . $discipline . "\"' name='subExport' /></p>";
    } else {
        echo "<p>Vous n'avez pas choisi soit le niveau, soit la discipline.</p>";
    }
} else {
    echo "<p>Vous n'&ecirc;tes pas connect&eacute; en tant qu'administrateur.</p>";
}
?>