<?php
if ( !empty( $_POST[ 'slogin' ] ) ) {
	if ( ( !empty( $_POST[ 'username' ] ) ) && ( !empty( $_POST[ 'password' ] ) ) ) {
		$username      = $link->real_escape_string( $_POST[ 'username' ] );
		$passwordprof  = md5( $link->real_escape_string( $_POST[ 'password' ] ) );
		$passwordeleve = encrypt( $link->real_escape_string( $_POST[ 'password' ] ) );
		$sqlprofs      = $link->query( "SELECT * FROM " . $prefix . "profs WHERE username='$username' AND password='$passwordprof'" );
		if ( $sqlprofs->num_rows == 0 ) {
			$sqleleves = $link->query( "SELECT * FROM " . $prefix . "eleves WHERE username='$username' AND password='$passwordeleve'" );
			if ( $sqleleves->num_rows == 0 ) {
				$_SESSION = array();
				header( 'Location: ' . linkapp() );
			} else {
				$_SESSION[ 'acusername' ] = $username;
				$_SESSION[ 'acpassword' ] = $passwordeleve;
				$s                        = mysqli_fetch_array( $sqleleves );
				$_SESSION[ 'acnom' ]      = $s[ 'nom' ];
				$_SESSION[ 'acprenom' ]   = $s[ 'prenom' ];
				$_SESSION[ 'actype' ]     = "eleves";
				header( 'Location: ' . linkapp() . 'bilaneleve.php' );
			}
		} else {
			$s                        = mysqli_fetch_array( $sqlprofs );
			$_SESSION[ 'acnom' ]      = $s[ 'nom' ];
			$_SESSION[ 'acprenom' ]   = $s[ 'prenom' ];
			$_SESSION[ 'acusername' ] = $username;
			$_SESSION[ 'acpassword' ] = $passwordprof;
			$_SESSION[ 'actype' ]     = "profs";
		}
	}
}
if ( !empty( $_POST[ 'slogout' ] ) ) {
	$_SESSION = array();
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, user-scalable=no, initial-scale=1, maximum-scale=1">
	<meta name="apple-mobile-web-app-capable" content="yes" />
	<meta name="apple-mobile-web-app-status-bar-style" content="white" />
	<meta name="apple-touch-fullscreen" content="yes" />
	<title><?php
if ( $nompage != "Accueil" ) {
	echo $nompage . " | " . $nomapp;
} else {
	echo $nomapp;
}
?></title>
	<link rel="icon" type="image/png" href="../contents/icon/icon.png" />
	<link rel="apple-touch-icon" href="../contents/icon/apple-touch-icon.png" />
	<link href="https://fonts.googleapis.com/css?family=Open+Sans:400,400i,700|Titillium+Web" rel="stylesheet">
	<link rel="stylesheet" type="text/css" href="<?php
echo $dir;
?>ext/jPushMenu/jPushMenu.css" />
	<link rel="stylesheet" type="text/css" href="<?php
echo $dir;
?>style.css" />
	<link rel="stylesheet" type="text/css" href="<?php
echo $dir;
?>ext/MultipleSelect/multiple-select.css" />
	<script src="<?php
echo $dir;
?>ext/JQuery/jquery-3.1.0.min.js"></script>
	<script src="<?php
echo $dir;
?>ext/jPushMenu/jPushMenu.js"></script>
	<script src="<?php
echo $dir;
?>ext/MultipleSelect/multiple-select.js"></script>
	<script src="<?php
echo $dir;
?>ext/ClipBoard/clipboard.js"></script>
	<script type="text/javascript">
	jQuery(document).ready(function($) {
		$('.toggle-menu').jPushMenu();
		$('.multselect').multipleSelect();
		var clipboardManuel = new Clipboard('#copy-button-manuel');
		clipboardManuel.on('success', function(e) {
		    e.clearSelection();
		});
		var clipboardCours = new Clipboard('#copy-button-cours');
		clipboardCours.on('success', function(e) {
		    e.clearSelection();
		});
		var clipboardAcnote = new Clipboard('#copy-button-acnote');
		clipboardAcnote.on('success', function(e) {
		    e.clearSelection();
		});
		var clipboardSsnote = new Clipboard('#copy-button-ssnote');
		clipboardSsnote.on('success', function(e) {
		    e.clearSelection();
		});
		var clipboardRattra = new Clipboard('#copy-button-rattra');
		clipboardRattra.on('success', function(e) {
		    e.clearSelection();
		});
	});
	</script>
	<?php
$detect = new Mobile_Detect();
?>
</head>
<body>
	<nav  class="cbp-spmenu cbp-spmenu-vertical cbp-spmenu-left">
		<ul class="nv-menu">
			<?php
include( "menu.php" );
?>
		</ul>
	</nav>
	<div id="wrapper">
	<a href="#menu" class="menu-trigger toggle-menu menu-left push-body"><img src="../contents/images/menu.png" /></a>
		<div id="header">
			<div id="header-top">
				<div id="logo">
					<h1><a href="<?php
echo linkapp();
?>"><?php
echo $nomapp . "</a></h1><p>" . $descapp . "</p>";
?>
				</div>
				<div id="connect-form">
					<?php
if ( estconnecte() ) {
	echo "<span style='line-height:2.5em;'>" . $_SESSION[ 'acprenom' ] . " " . $_SESSION[ 'acnom' ];
	if ( estprof() ) {
		echo " [<a href='myaccount.php'>Mon compte</a>]";
	}
	echo "</span>";
	echo "<form name='logout' action='index.php' method='POST'>";
	echo "<input value='Se d&eacute;connecter' type='submit' name='slogout' />";
	echo "</form>";
} else {
	echo "<form name='login' action='evaluation.php' method='POST'>";
	echo "<label>Nom d'utilisateur : </label><input type='text' name='username' id='username' /><br />";
	echo "<label>Mot de passe : </label><input type='password' name='password' id='password' /><br />";
	echo "<input type='submit' value='Se connecter' name='slogin' class='submit' /></form>";
}
?>
				</div>
				<div class="clear"></div>
			</div>
			<div id="navigation">
				<div class="main-menu">
					<ul class="sf-menu">
						<?php
include( "menu.php" );
?>
					</ul>
				</div>
			</div>
		</div>
		<div id="content" role="main">
			<?php
if ( $detect->isMobile() && !$detect->isTablet() ) {
	if ( estconnecte() ) {
		echo "<form name='logout' action='index.php' method='POST' style='text-align:right;'>";
		echo "<span style='line-height:19px;'>" . $_SESSION[ 'acprenom' ] . " " . $_SESSION[ 'acnom' ] . "</span>";
		if ( estprof() ) {
			echo " [<a href='myaccount.php'>Mon compte</a>]";
		}
		echo "<br /><input value='Se d&eacute;connecter' type='submit' name='slogout' />";
		echo "</form>";
	} else {
		echo "<h1>Se connecter</h1>";
		echo "<form name='login' action='evaluation.php' method='POST'>";
		echo "<label>Nom d'utilisateur : </label><input type='text' name='username' id='username' /><br />";
		echo "<label>Mot de passe : </label><input type='password' name='password' id='password' /><br />";
		echo "<input type='submit' value='Se connecter' name='slogin' class='submit' /></form>";
	}
}
?>