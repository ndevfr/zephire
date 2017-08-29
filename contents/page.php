<?php
$pg = !empty( $_GET[ 'pg' ] ) ? $_GET[ 'pg' ] : NULL;
switch ( $pg ) {
	case "accueil":
		$inclpage = "accueil.php";
		$nompage  = "Accueil";
		break;
	case "api":
		$inclpage = "api.php";
		$nompage  = "API";
		break;
	case "backup":
		$inclpage = "backup.php";
		$nompage  = "Backup";
		break;
	case "bilanclasse":
		$inclpage = "bilanclasse.php";
		$nompage  = "Bilan classe";
		break;
	case "bilaneleve":
		$inclpage = "bilaneleve.php";
		if ( estprof() ) {
			$nompage = "Bilan &eacute;l&egrave;ve";
		} else {
			$nompage = "Mes &eacute;valuations";
		}
		break;
	case "evaluation":
		$inclpage = "evaluation.php";
		$nompage  = "Evaluer";
		break;
	case "exportcsv":
		$inclpage = "exportcsv.php";
		$nompage  = "Export CSV";
		break;
	case "ficheevaluation":
		$inclpage = "ficheevaluation.php";
		$nompage  = "Fiche &eacute;valuation";
		break;
	case "hasard":
		$inclpage = "hasard.php";
		$nompage  = "Hasard";
		break;
	case "install":
		$inclpage = "install.php";
		$nompage  = "Installation";
		break;
	case "latex":
		$inclpage = "latex.php";
		$nompage  = "Latex";
		break;
	case "lstidentifiants":
		$inclpage = "lstidentifiants.php";
		$nompage  = "Liste des identifiants";
		break;
	case "mdfbaremes":
		$inclpage = "mdfbaremes.php";
		$nompage  = "Barèmes";
		break;
	case "mdfclasses":
		$inclpage = "mdfclasses.php";
		$nompage  = "Modifier les classes";
		break;
	case "mdfcompetences":
		$inclpage = "mdfcompetences.php";
		$nompage  = "Modifier les comp&eacute;tences";
		break;
	case "mdfdisciplines":
		$inclpage = "mdfdisciplines.php";
		$nompage  = "Modifier les disciplines";
		break;
	case "mdfeleves":
		$inclpage = "mdfeleves.php";
		$nompage  = "Modifier les &eacute;l&egrave;ves";
		break;
	case "mdfniveaux":
		$inclpage = "mdfniveaux.php";
		$nompage  = "Modifier les niveaux";
		break;
	case "mdfoptions":
		$inclpage = "mdfoptions.php";
		$nompage  = "Personnalisation";
		break;
	case "mdfprofs":
		$inclpage = "mdfprofs.php";
		$nompage  = "Modifier les professeurs";
		break;
	case "mdfprogressions":
		$inclpage = "mdfprogressions.php";
		$nompage  = "Modifier la progression";
		break;
	case "mdfreperes":
		$inclpage = "mdfreperes.php";
		$nompage  = "Repères de progressivité des ceintures";
		break;
	case "moyclasse":
		$inclpage = "moyclasse.php";
		$nompage  = "Moyennes classe";
		break;
	case "myaccount":
		$inclpage = "myaccount.php";
		$nompage  = "Mon compte";
		break;
	case "progressions":
		$inclpage = "progressions.php";
		$nompage  = "Progressions";
		break;
	case "reinitialisation":
		$inclpage = "reinitialisation.php";
		$nompage  = "R&eacute;initialisation";
		break;
	case "selectcompetences":
		$inclpage = "selectcompetences.php";
		$nompage  = "Compétences abordées dans le chapitre";
		break;
	default:
		$inclpage = "accueil.php";
		$nompage  = "Accueil";
		break;
}
?>