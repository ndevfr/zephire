<h1>Utilisation</h1>
	<p>Pour utiliser l'application, connectez-vous en utilisant le formulaire situ&eacute; en haut &agrave; droite de l'&eacute;cran. En cas d'oubli du mot de passe, contacter votre administrateur. Il peut le r&eacute;initialiser.</p>
<?php
if ( !$detect->isMobile() || $detect->isTablet() ) {
?>
	<p>Pour naviguer dans l'application, utilisez le menu horizontal ci-dessus.</p>
<?php
} else {
?>
	<p>Pour naviguer dans l'application, utilisez le menu vertical en l'affichant grâce au bouton en haut à gauche.</p>
<?php
}
?>
	<p>Une fois votre navigation termin&eacute;e, pensez &agrave; vous d&eacute;connecter de l'application (bouton situ&eacute; en haut &agrave; droite de l'&eacute;cran).</p>
<?php
// Recupération du message d'accueil dans les options
$result = $link->query( "SELECT * FROM " . $prefix . "options WHERE id = 'msgacc'" );
$r      = mysqli_fetch_array( $result );
$text   = $r[ 'valeur' ];
echo $text;
// Affichage des informations déstinées aux administrateurs
if ( estadmin() ) {
	include( 'version.php' );
	include( 'notifupdate.php' );
}
?>