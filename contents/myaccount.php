<?php
echo "<h1>Mon compte</h1>";
if (estprof()) {
    $message = "";
    if (!empty($_POST['submit'])) {
        if ($_SESSION['acpassword'] == md5($_POST['actpassword'])) {
            if ($_POST['newpassword1'] == $_POST['newpassword2']) {
                $username    = $_SESSION['acusername'];
                $password    = $_SESSION['acpassword'];
                $newpassword = md5($link->real_escape_string($_POST['newpassword1']));
                $sql         = "UPDATE " . $prefix . "profs SET password = \"$newpassword\" WHERE username='$username' AND password='$password'";
                $link->query($sql);
                $message = "Le mot de passe a été modifié.";
            } else {
                $message = "Les deux mots de passe ne correspondent pas.";
            }
        } else {
            $message = "Le mot de passe actuel n'est pas valide.";
        }
    }
    echo "<form action='myaccount.php' method='POST'>";
    echo "<p>Sur cette page, vous pouvez modifier votre mot de passe.</p>";
    echo "<table class='noborder'><tr><td class='noborder' style='text-align:right;'>Mot de passe actuel :</td><td class='noborder'><input type='password' name='actpassword' value='' placeholder='Mot de passe actuel' /></td></tr>";
    echo "<tr><td class='noborder' style='text-align:right;'>Nouveau mot de passe :</td><td class='noborder'><input type='password' name='newpassword1' value='' placeholder='Nouveau mot de passe' /></td></tr>";
    echo "<tr><td class='noborder' style='text-align:right;'>Répéter le nouveau mot de passe :</td><td class='noborder'><input type='password' name='newpassword2' value='' placeholder='Nouveau mot de passe' /></td></tr></table>";
    echo "<p><input name='submit' type='submit' value='Envoyer' /> <strong>$message</strong></p></form>";
}
?>