<?php
$pageaff = $inclpage;
// Page "Accueil"
echo "<li id='sidebar_menu_home' class='page_item";
if ( $pageaff == "accueil.php" ) {
	echo " active Selected";
}
echo "'><a href='" . linkapp() . "'><span class='abs'></span>Accueil</a></li>";
if ( estconnecte() ) {
	if ( estprof() ) {
		// Page "Evaluer mes eleves"
		echo "<li class='page_item";
		if ( $pageaff == "evaluation.php" ) {
			echo " active Selected";
		}
		echo "'><a href='evaluation.php'>Evaluer</a></li>";
		// Page "Bilan eleve"
		echo "<li class='page_item";
		if ( $pageaff == "bilaneleve.php" ) {
			echo " active Selected";
		}
		echo "'><a href='bilaneleve.php'>Bilan &eacute;l&egrave;ve</a></li>";
		// Page "Bilan classe"
		echo "<li class='page_item";
		if ( $pageaff == "bilanclasse.php" ) {
			echo " active Selected";
		}
		echo "'><a href='bilanclasse.php'>Bilan classe</a></li>";
		// Page "Moyennes classe"
		echo "<li class='page_item";
		if ( $pageaff == "moyclasse.php" ) {
			echo " active Selected";
		}
		echo "'><a href='moyclasse.php'>Moyennes classe</a></li>";
		// Page "Voir les identifiants"
		echo "<li class='page_item";
		if ( $pageaff == "lstidentifiants.php" ) {
			echo " active Selected";
		}
		echo "'><a href='lstidentifiants.php'>Identifiants</a></li>";
	} else {
		// Page "Mes evaluations"
		echo "<li class='page_item";
		if ( $pageaff == "bilaneleve.php" ) {
			echo " active Selected";
		}
		echo "'><a href='bilaneleve.php'>Mes &eacute;valuations</a></li>";
	}
}
echo "<li class='page_item";
if ( $pageaff == "progressions.php" ) {
	echo " active Selected";
}
echo "'><a href='progressions.php'><span class='abs'></span>Progressions</a></li>";
if ( !$detect->isMobile() || $detect->isTablet() ) {
	if ( estprof() ) {
		echo "<li><a href='#' >Options</a><ul class='children'>";
		$result    = $link->query( "SELECT * FROM " . $prefix . "options WHERE id = 'lienperso'" );
		$r         = mysqli_fetch_array( $result );
		$lienperso = explode( "\n", $r[ 'valeur' ] );
		foreach ( $lienperso as $lien ) {
			echo "<li class='page_item";
			preg_match( "/href=\'(.*)\'>/", $lien, $regs );
			$url = $regs[ 1 ];
			if ( $pageaff == $url ) {
				echo " child-active child-Selected";
			}
			echo "'>" . $lien . "</li>";
		}
		if ( estadmin() ) {
			// Page "Modifier les niveaux"
			echo "<li class='page_item";
			if ( $pageaff == "mdfniveaux.php" ) {
				echo " child-active child-Selected";
			}
			echo "'><a href='mdfniveaux.php'>Modifier Niveaux</a></li>";
			// Page "Modifier les classes"
			echo "<li class='page_item";
			if ( $pageaff == "mdfclasses.php" ) {
				echo " child-active child-Selected";
			}
			echo "'><a href='mdfclasses.php'>Modifier Classes</a></li>";
			// Page "Modifier les disciplines"
			echo "<li class='page_item";
			if ( $pageaff == "mdfdisciplines.php" ) {
				echo " child-active child-Selected";
			}
			echo "'><a href='mdfdisciplines.php'>Modifier Disciplines</a></li>";
			// Page "Modifier les professeurs"
			echo "<li class='page_item";
			if ( $pageaff == "mdfprofs.php" ) {
				echo " child-active child-Selected";
			}
			echo "'><a href='mdfprofs.php'>Modifier Professeurs</a></li>";
			// Page "Modifier les eleves"
			echo "<li class='page_item";
			if ( $pageaff == "mdfeleves.php" ) {
				echo " child-active child-Selected";
			}
			echo "'><a href='mdfeleves.php'>Modifier El&egrave;ves</a></li>";
		}
		// Page "Modifier les competences"
		echo "<li class='page_item";
		if ( $pageaff == "mdfcompetences.php" ) {
			echo " child-active child-Selected";
		}
		echo "'><a href='mdfcompetences.php'>Modifier Comp&eacute;tences</a></li>";
		// Page "Modifier la progression"
		echo "<li class='page_item";
		if ( $pageaff == "mdfprogressions.php" ) {
			echo " child-active child-Selected";
		}
		echo "'><a href='mdfprogressions.php'>Modifier Progressions</a></li>";
		if ( estadmin() ) {
			// Page "Modifier les options"
			echo "<li class='page_item";
			if ( $pageaff == "mdfoptions.php" ) {
				echo " child-active child-Selected";
			}
			echo "'><a href='mdfoptions.php'>Personnalisation</a></li>";
			// Page "Reinitialiser classes et eleves"
			echo "<li class='page_item'><a href='reinitialisation.php'>R&eacute;initialisation</a></li>";
			// Page "Faire un backup"
			echo "<li class='page_item'><a href='backup.php'>Sauvegarder BDD</a></li>";
		}
	}
}
?>