<?php
$nombr1 = rand(1, 5);
$nombr2 = rand(1, 5);
while ($nombr2 == $nombr1) {
    $nombr2 = rand(1, 5);
}
$nombr3 = rand(1, 5);
while (($nombr3 == $nombr1) OR ($nombr3 == $nombr2)) {
    $nombr3 = rand(1, 5);
}
$nbs = array(
    $nombr1,
    $nombr2,
    $nombr3
);
asort($nbs);
$lnbs   = implode("", $nbs);
$txtnbs = implode(", ", $nbs);
echo "<h1>R&eacute;initialisation</h1>";
if (estadmin()) {
    echo "<form action='' method='POST'>";
    echo "<p>Pour r&eacute;initialiser la base de donn&eacute;es (&eacute;l&egrave;ves et &eacute;valuations), compl&eacute;tez les champs suivants :</p>";
    echo "<p>Confirmez votre mot de passe : <input type='password' name='passwd' /></p>";
    echo "<p>Cochez uniquement les cases $txtnbs :  <input type='checkbox' name='cks[]' value='1' /> <input type='checkbox' name='cks[]' value='2' /> <input type='checkbox' name='cks[]' value='3' /> <input type='checkbox' name='cks[]' value='4' /> <input type='checkbox' name='cks[]' value='5' /></p>";
    echo "<p class='noprint'><input type='hidden' name='lnbs' value='$lnbs' /><input type='submit' value='Valider' name='submit' /></p></form>";
    
    if ((!empty($_POST['submit']))) {
        if (encrypt($_POST['passwd']) == $_SESSION['acpassword']) {
            $txtckok = "";
            foreach ($_POST['cks'] as $ck) {
                $txtckok .= $ck;
            }
            if ($txtckok == $_POST['lnbs']) {
                $link->query("SET SQL_MODE='NO_AUTO_VALUE_ON_ZERO';");
                $raz = $link->query("TRUNCATE TABLE `" . $prefix . "eleves`; TRUNCATE TABLE `" . $prefix . "evaluations`;");
                if ($raz == true) {
                    echo "<p>Suppression des &eacute;l&egrave;ves et &eacute;valuations ...... <span style='color:green;font-weight:bold;'>OK</span></p>";
                } else {
                    echo "<p>Suppression des &eacute;l&egrave;ves et &eacute;valuations ...... <span style='color:red;font-weight:bold;'>Erreur !</span></p>";
                }
            } else {
                echo "<p>Vous n'avez pas coch&eacute; les bonnes cases.</p>";
            }
        } else {
            echo "<p>Votre mot de passe n'est pas correct.</p>";
        }
    }
} else {
    echo "<p>Vous n'&ecirc;tes pas connect&eacute; en tant qu'administrateur.</p>";
}
?>