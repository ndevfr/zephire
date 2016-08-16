<?php
echo "<h1>Installation</h1>";
$lesmessages = "";
if ( !empty($_POST['submit']) ) {
	$lesmessages .= "<ul>";
	$link->query("SET SQL_MODE='NO_AUTO_VALUE_ON_ZERO';");
	$raz = $link->query("DROP TABLE `".$prefix."chapitres`, `".$prefix."classes`, `".$prefix."competences`, `".$prefix."disciplines`, `".$prefix."eleves`, `".$prefix."evaluations`, `".$prefix."niveaux`, `".$prefix."notations`, `".$prefix."options`, `".$prefix."profs`;");
	$lesmessages .= "<li>Suppression des anciennes bases de l'application ...... <span style='color:green;font-weight:bold;'>OK</span></li>";
	$chapitres = $link->query("CREATE TABLE IF NOT EXISTS `".$prefix."chapitres` (
		`id` varchar(255) NOT NULL,
		`nom` varchar(255) NOT NULL,
		`competences` varchar(255) NOT NULL,
		`baremes` varchar(255) NOT NULL,
		`ceintures` text NOT NULL,
		`nbevaluations` int(11) NOT NULL,
		`autoevaluation` tinyint(1) NOT NULL,
		`mode` tinyint(1) NOT NULL,
		`trimestre` tinyint(1) NOT NULL,
		`date` date NOT NULL,
		`classe` varchar(255) NOT NULL,
		`discipline` varchar(255) NOT NULL,
		`libelle` varchar(255) NOT NULL,
		PRIMARY KEY (`id`)
	) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE utf8_general_ci;");
	if ( $chapitres == true ) {
		$lesmessages .= "<li>Installation de la base contenant les chapitres ...... <span style='color:green;font-weight:bold;'>OK</span></li>";
	} else {
		$lesmessages .= "<li>Installation de la base contenant les chapitres ...... <span style='color:red;font-weight:bold;'>Erreur !</span></li>";
	}
	$classes = $link->query("CREATE TABLE IF NOT EXISTS `".$prefix."classes` (
		`id` varchar(255) NOT NULL,
		`nom` varchar(255) NOT NULL,
		`niveau` varchar(255) NOT NULL,
		PRIMARY KEY (`id`)
	) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE utf8_general_ci;");
	if ( $classes == true ) {
		$lesmessages .= "<li>Installation de la base contenant les classes ...... <span style='color:green;font-weight:bold;'>OK</span></li>";
	} else {
		$lesmessages .= "<li>Installation de la base contenant les classes ...... <span style='color:red;font-weight:bold;'>Erreur !</span></li>";
	}
	$competences = $link->query("CREATE TABLE IF NOT EXISTS `".$prefix."competences` (
		`id` varchar(255) NOT NULL,
		`nom` varchar(255) NOT NULL,
		`cat` int(11) NOT NULL,
		`socle` varchar(255) NOT NULL,
		`niveau` varchar(255) NOT NULL,
		`discipline` varchar(255) NOT NULL,
		`libelle` varchar(255) NOT NULL,
		PRIMARY KEY (`id`)
	) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE utf8_general_ci;");
	if ( $competences == true ) {
		$lesmessages .= "<li>Installation de la base contenant les comp&eacute;tences ...... <span style='color:green;font-weight:bold;'>OK</span></li>";
	} else {
		$lesmessages .= "<li>Installation de la base contenant les comp&eacute;tences ...... <span style='color:red;font-weight:bold;'>Erreur !</span></li>";
	}
	$disciplines = $link->query("CREATE TABLE IF NOT EXISTS `".$prefix."disciplines` (
		`id` varchar(255) NOT NULL,
		`nom` varchar(255) NOT NULL,
		`active` tinyint(1) NOT NULL,
		PRIMARY KEY (`id`)
	) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE utf8_general_ci;");
	if ( $disciplines == true ) {
		$lesmessages .= "<li>Installation de la base contenant les disciplines ...... <span style='color:green;font-weight:bold;'>OK</span></li>";
	} else {
		$lesmessages .= "<li>Installation de la base contenant les disciplines ...... <span style='color:red;font-weight:bold;'>Erreur !</span></li>";
	}
	$eleves = $link->query("CREATE TABLE IF NOT EXISTS `".$prefix."eleves` (
		`id` int(11) NOT NULL AUTO_INCREMENT,
		`nom` varchar(255) NOT NULL,
		`prenom` varchar(255) NOT NULL,
		`sexe` varchar(255) NOT NULL,
		`datenaissance` varchar(255) NOT NULL,
		`regime` varchar(255) NOT NULL,
		`options` text NOT NULL,
		`commentaires` text NOT NULL,
		`username` varchar(255) NOT NULL,
		`password` varchar(255) NOT NULL,
		`classe` int(11) NOT NULL,
		PRIMARY KEY (`id`)
	) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE utf8_general_ci;");
	if ( $eleves == true ) {
		$lesmessages .= "<li>Installation de la base contenant les &eacute;l&egrave;ves ...... <span style='color:green;font-weight:bold;'>OK</span></li>";
	} else {
		$lesmessages .= "<li>Installation de la base contenant les &eacute;l&egrave;ves ...... <span style='color:red;font-weight:bold;'>Erreur !</span></li>";
	}
	$evaluations = $link->query("CREATE TABLE IF NOT EXISTS `".$prefix."evaluations` (
		`id` varchar(255) NOT NULL,
		`competences` text NOT NULL,
		`evaluations` text NOT NULL,
		`bilan` varchar(255) NOT NULL,
		`absent` tinyint(1) NOT NULL,
		`nonnote` tinyint(1) NOT NULL,
		`classe` varchar(255) NOT NULL,
		`discipline` varchar(255) NOT NULL,
		`chapitre` varchar(255) NOT NULL,
		`eleve` int(11) NOT NULL,
		PRIMARY KEY (`id`)
	) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE utf8_general_ci;");
	if ( $evaluations == true ) {
		$lesmessages .= "<li>Installation de la base contenant les &eacute;valuations ...... <span style='color:green;font-weight:bold;'>OK</span></li>";
	} else {
		$lesmessages .= "<li>Installation de la base contenant les &eacute;valuations ...... <span style='color:red;font-weight:bold;'>Erreur !</span></li>";
	}
	$niveaux = $link->query("CREATE TABLE IF NOT EXISTS `".$prefix."niveaux` (
		`id` varchar(255) NOT NULL,
		`nom` varchar(255) NOT NULL,
		PRIMARY KEY (`id`)
	) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE utf8_general_ci;");
	if ( $niveaux == true ) {
		$lesmessages .= "<li>Installation de la base contenant les niveaux ...... <span style='color:green;font-weight:bold;'>OK</span></li>";
	} else {
		$lesmessages .= "<li>Installation de la base contenant les niveaux ...... <span style='color:red;font-weight:bold;'>Erreur !</span></li>";
	}
	$notations = $link->query("CREATE TABLE IF NOT EXISTS `".$prefix."notations` (
		`id` varchar(255) NOT NULL,
		`libelles` text NOT NULL,
		`descriptions` text NOT NULL,
		`notations` text NOT NULL,
		`icones` varchar(255) NOT NULL,
		`classe` varchar(255) NOT NULL,
		`discipline` varchar(255) NOT NULL,
		PRIMARY KEY (`id`)
	) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE utf8_general_ci;");
	if ( $notations == true ) {
		$lesmessages .= "<li>Installation de la base contenant les options de notations ...... <span style='color:green;font-weight:bold;'>OK</span></li>";
	} else {
		$lesmessages .= "<li>Installation de la base contenant les options de notations ...... <span style='color:red;font-weight:bold;'>Erreur !</span></li>";
	}
	$options = $link->query("CREATE TABLE IF NOT EXISTS `".$prefix."options` (
		`id` varchar(255) NOT NULL,
		`valeur` text NOT NULL,
		PRIMARY KEY (`id`)
	) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE utf8_general_ci;");
	if ( $options == true ) {
		$lesmessages .= "<li>Installation de la base contenant les options ...... <span style='color:green;font-weight:bold;'>OK</span></li>";
	}else{
		$lesmessages .= "<li>Installation de la base contenant les options ...... <span style='color:red;font-weight:bold;'>Erreur !</span></li>";
	}
	$Option1 = $link->query("INSERT INTO ".$prefix."options (id, valeur) VALUES (\"msgacc\",\"<h1>Message d'accueil</h1><p>Voici un exemple de message d'accueil</p>\")");
	$Option2 = $link->query("INSERT INTO ".$prefix."options (id, valeur) VALUES (\"lienperso\",\"<a href='exemple.php'>Exemple lien</a>\")");
	$installopt=$Option1&&$Option2;
	if ( $installopt ) {
		$lesmessages .= "<li>Ajout des options par d&eacute;faut ...... <span style='color:green;font-weight:bold;'>OK</span></li>";
	} else {
		$lesmessages .= "<li>Ajout des options par d&eacute;faut ...... <span style='color:red;font-weight:bold;'>Erreur !</span></li>";
	}
	$profs = $link->query("CREATE TABLE IF NOT EXISTS `".$prefix."profs` (
		`id` int(11) NOT NULL AUTO_INCREMENT,
		`nom` varchar(255) NOT NULL,
		`prenom` varchar(255) NOT NULL,
		`classes` text NOT NULL,
		`disciplines` text NOT NULL,
		`username` varchar(255) NOT NULL,
		`password` varchar(255) NOT NULL,
		`admin` tinyint(1) NOT NULL,
		PRIMARY KEY (`id`)
	) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE utf8_general_ci;");
	if ( $profs == true ) {
		$lesmessages .= "<li>Installation de la base contenant les professeurs ...... <span style='color:green;font-weight:bold;'>OK</span></li>";
	} else {
		$lesmessages .= "<li>Installation de la base contenant les professeurs ...... <span style='color:red;font-weight:bold;'>Erreur !</span></li>";
	}
	$whoisadmin = $link->query("SELECT * FROM ".$prefix."profs WHERE admin = 1");
	if ( $whoisadmin->num_rows == 0 ) {
		$compteadmin=$link->query("INSERT INTO ".$prefix."profs (nom, prenom, classes, username, password, admin) VALUES ('Z&eacute;phire','Administrateur','', 'admin', '".md5('zephire')."', 1)");
		if ( $compteadmin ) {
			$lesmessages .= "<li>Ajout du compte administrateur par d&eacute;faut : admin / zephire ...... <span style='color:green;font-weight:bold;'>OK</span></li>";
		} else {
			$lesmessages .= "<li>Ajout du compte administrateur par d&eacute;faut : admin / zephire ...... <span style='color:red;font-weight:bold;'>Erreur !</span></li>";
		}
	}else{
		$lesmessages .= "<li>Un compte administrateur existe d&eacute;j&agrave; ...... <span style='color:green;font-weight:bold;'>OK</span></li>";
	}
	$lesmessages .= "</ul>";
}else{
	$lesmessages .= "<p>Pour confirmer l'installation de la base de donn&eacute;es, cliquez sur le bouton ci-dessous :</p>";
	$lesmessages .= "<form action='' method='POST'><input type='submit' name='submit' value=\"Installer la base de donn&eacute;es\" /></form>";
}
echo "<p>Ce script installe la base de donn&eacute;es n&eacute;cessaire &agrave; l'utilisation de l'application.</p>";
echo $lesmessages;
echo "<p>Une fois l'installation effectu&eacute;e, supprimmez le fichier install.php du serveur.</p>";
?>