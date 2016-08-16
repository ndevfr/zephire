<?php
$classe      = !empty( $_GET[ 'idcl' ] ) ? $_GET[ 'idcl' ] : NULL;
$classe      = verifclasse( $link->real_escape_string( $classe ) );
$discipline  = !empty( $_GET[ 'idds' ] ) ? $_GET[ 'idds' ] : NULL;
$discipline  = verifdiscipline( $link->real_escape_string( $discipline ) );
$chapitre    = !empty( $_GET[ 'idch' ] ) ? $_GET[ 'idch' ] : NULL;
$chapitre    = verifchapitre( $link->real_escape_string( $chapitre ), $classe, $discipline );
$idchapitre  = substr( $chapitre, strlen( $classe ) + strlen( $discipline ) + 2 );
$ceinturesnv = $ceintures;
unset( $ceinturesnv[ 0 ] );
if ( !empty( $classe ) ) {
	$result = $link->query( "SELECT * FROM " . $prefix . "classes WHERE id = '$classe'" );
	$r      = mysqli_fetch_array( $result );
	$niveau = $r[ 'niveau' ];
	$nomcl  = $r[ 'nom' ];
}
if ( !empty( $chapitre ) ) {
	$infoch  = infoch( $chapitre );
	$titrech = $infoch[ 'nom' ];
} else {
	$chapitre = "";
}
?>
<script type="text/javascript">
function updateTotal(){
	var baremes = document.getElementsByName('baremes[]');
	var total = 0;
	for (var i = 0, iMax = baremes.length; i < iMax; ++i) {
		var bareme = parseFloat(baremes[i].value);
		total = total + bareme;
	}
	document.getElementById("total").innerHTML = total;
}
</script>
<?php
echo "<h1>$nomcl : $titrech</h1>";
echo "<h2>$nompage</h2>";
if ( ( !empty( $classe ) ) && ( !empty( $discipline ) ) && ( !empty( $chapitre ) ) ) {
	if ( !empty( $_POST[ 'submit' ] ) ) {
		$lstbaremes = "";
		foreach ( $_POST[ 'baremes' ] as $bareme ) {
			$lstbaremes .= $bareme . ",";
		}
		$lstbaremes = substr( $lstbaremes, 0, -1 );
		$sql        = "UPDATE " . $prefix . "chapitres SET baremes = \"$lstbaremes\" WHERE id = '$chapitre'";
		$link->query( $sql );
	}
	$infoch = infoch( $chapitre );
	$compch = $infoch[ 'competences' ];
	$barmch = $infoch[ 'baremes' ];
	$result = $link->query( "SELECT * FROM " . $prefix . "competences WHERE id in ($compch) ORDER BY cat ASC, id ASC" );
	$i      = 0;
	$total  = 0;
	while ( $r = mysqli_fetch_array( $result ) ) {
		$comp[ $i ][ 'id' ]      = $r[ 'id' ];
		$comp[ $i ][ 'cat' ]     = $r[ 'cat' ];
		$comp[ $i ][ 'nom' ]     = stripslashes( $r[ 'nom' ] );
		$comp[ $i ][ 'socle' ]   = $r[ 'socle' ];
		$comp[ $i ][ 'libelle' ] = substr( $comp[ $i ][ 'id' ], strlen( $niveau ) );
		$comp[ $i ][ 'bareme' ]  = $barmch[ $comp[ $i ][ 'id' ] ];
		$total += $comp[ $i ][ 'bareme' ];
		$i++;
	}
	echo "<form action='mdfbaremes.php?idcl=" . $classe . "&amp;idds=" . $discipline . "&amp;idch=" . $idchapitre . "' method='POST'><p>Pour chaque compétence, indiquer les points qui lui sont attribuée.</p><table class='data large'><thead><tr><th>Nom</th><th style='width:50px;'>Barème</th></tr></thead><tbody>";
	for ( $i = 0; $i < sizeof( $comp ); $i++ ) {
		echo "<tr><td style='width:100%;text-align:left;'><strong>" . ( substr( $comp[ $i ][ 'id' ], strlen( $niveau ) + strlen( $discipline ) ) ) . "</strong> " . $comp[ $i ][ 'nom' ];
		if ( !empty( $comp[ $i ][ 'socle' ] ) ) {
			echo " [" . $comp[ $i ][ 'socle' ] . "]";
		}
		echo "</td>";
		echo "<td style='width:50px;'><input type='number' pattern='[0-9]' min='0' step='0.5' class='inputnote' onchange='javascript:updateTotal();' style='text-align:center;' value='" . $comp[ $i ][ 'bareme' ] . "' name='baremes[]' /></td></tr>";
	}
	echo "<tr><td style='width:100%;text-align:left;'><strong>TOTAL</strong></td>";
	echo "<td style='width:50px;height:30px;'><div id='total' style='font-weight:bold;padding-right:10px;'>$total</div></td></tr>";
	echo "</table>";
	echo "<p><input name='submit' type='submit' value='Modifier' /> <input name='retour' type='button' value='Retour' onclick='window.location.href=\"mdfprogressions.php?idcl=$classe&idds=$discipline\"' /></p></form>";
}
?>