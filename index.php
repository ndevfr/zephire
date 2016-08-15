<?php

/**************************************************/
/******** Informations sur l'etablissement ********/
/**************************************************/

// RNE de l'etablissement
$RNE = "********";
// Nom de l'etablissement
$SCH = "********";

/**************************************************/
/****** Informations de connections à la BDD ******/
/**************************************************/

// Adresse de la base de donnees
$dbhost = "********";
// Nom de la base de donnees
$dbname = "********";
// Nom d'utilisateur
$dbuser = "********";
// Mot de passe utilisateur
$dbpass = "********";
// Prefixe utilise pour la base de donnees
$prefix = "********_";

/**************************************************/
/**************** Début du contenu ****************/
/**************************************************/

if(is_dir("contents")){
	$dir = "contents/";
}else{
	$dir = "../contents/";
}

include ($dir . "contenu.php");

?>