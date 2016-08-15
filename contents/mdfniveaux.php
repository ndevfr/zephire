<?php
echo "<h1>Modifier les niveaux</h1>";
if (estadmin()) {
    if ((!empty($_POST['submit']))) {
        for ($i = 1; $i <= sizeof($_POST['nom']); $i++) {
            $idniv  = $link->real_escape_string($_POST['id'][$i]);
            $nomniv = $link->real_escape_string($_POST['nom'][$i]);
            $sql    = "UPDATE " . $prefix . "niveaux SET nom = '$nomniv' WHERE id = $idniv";
            $link->query($sql);
        }
        if (!empty($_POST['nomnew'])) {
            $nomniv = $link->real_escape_string($_POST['nomnew']);
            $idniv  = $link->real_escape_string($_POST['idnew']);
            $sql    = "INSERT INTO " . $prefix . "niveaux (id, nom) VALUES ('$idniv', '$nomniv')";
            $link->query($sql);
        }
    }
    if ((!empty($_POST['subSuppr'])) && (!empty($_POST['supprniv']))) {
        $supprniv = $link->real_escape_string($_POST['supprniv']);
        $sql      = "DELETE FROM " . $prefix . "niveaux WHERE id = $supprniv";
        $link->query($sql);
    }
    if ((!empty($_POST['subUpload'])) && (!empty($_FILES['fichiercsv']['tmp_name']))) {
        $sql = "DELETE FROM " . $prefix . "niveaux";
        $link->query($sql);
        $fichiercsv = $_FILES['fichiercsv']['tmp_name'];
        $fic        = fopen("$fichiercsv", 'rb');
        for ($ligne = fgetcsv($fic, 1024, ";"); !feof($fic); $ligne = fgetcsv($fic, 1024, ";")) {
            $symbole = substr($ligne[0], 0, 1);
            if ($symbole !== "#") {
                $id  = $link->real_escape_string($ligne[0]);
                $nom = $link->real_escape_string(addslashes($ligne[1]));
                $sql = "INSERT INTO " . $prefix . "niveaux (id, nom) VALUES ($id, '$nom')";
                $link->query($sql);
            }
        }
        fclose($fic);
    }
    echo "<form action='mdfniveaux.php' method='POST'><table class='data'><thead><tr><th>Id</th><th>Nom</th></tr></thead><tbody>";
    $result = $link->query("SELECT * FROM " . $prefix . "niveaux ORDER BY id DESC");
    $i      = 1;
    while ($r = mysqli_fetch_array($result)) {
        $nom = $r['nom'];
        $id  = $r['id'];
        echo "<tr>";
        echo "<td style='width:40px;'><input type='text' class='inputcell' value=\"$id\" name='id[$i]' /></td>";
        echo "<td><input type='text' class='inputcell' value=\"$nom\" name='nom[$i]' /></td>";
        echo "</tr>";
        $i++;
    }
    echo "<tr>";
    echo "<td style='width:10px;'><input type='text' class='inputcell' value='' name='idnew' placeholder='...' /></td>";
    echo "<td><input type='text' class='inputcell' value='' name='nomnew' placeholder='...' /></td>";
    echo "</tr></tbody></table>";
    echo "<p class='noprint'><input type='submit' value='Valider' name='submit' /></p></form>";
    $result = $link->query("SELECT * FROM " . $prefix . "niveaux");
    echo "<form action='mdfniveaux.php' name='formsuppr' method='POST' class='noprint'><p>Supprimer un niveau : <select name='supprniv'><option value='' selected>...</option>";
    while ($r = mysqli_fetch_array($result)) {
        $idcl  = $r['id'];
        $nomcl = $r['nom'];
        echo "<option value='$idcl'>$nomcl</option>";
    }
    echo "</select><input type='submit' value='Valider' name='subSuppr' /></p></form>";
    echo "<h2>Importation et exportation</h2>";
    echo "<form action='mdfniveaux.php' name='formupload' enctype='multipart/form-data' method='POST' class='noprint'><p>Importer un fichier CSV : <input type='file' name='fichiercsv' />";
    echo "<input type='submit' value='Valider' name='subUpload' /></p></form>";
    echo "<p class='noprint'>Exporter un fichier CSV : <input type='submit' value='Valider' onclick='location.href=\"exportcsv.php?cont=niveaux\"' name='subExport' /></p>";
} else {
    echo "<p>Vous n'&ecirc;tes pas connect&eacute; en tant qu'administrateur.</p>";
}
?>