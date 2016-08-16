<?php
echo "<h1>Moyennes classe</h1>";
if ( estprof() ) {
	$classe     = !empty( $_GET[ 'idcl' ] ) ? $_GET[ 'idcl' ] : NULL;
	$classe     = verifclasse( $link->real_escape_string( $classe ) );
	$discipline = !empty( $_GET[ 'idds' ] ) ? $_GET[ 'idds' ] : NULL;
	$discipline = verifdiscipline( $link->real_escape_string( $discipline ) );
	$chapitre   = !empty( $_GET[ 'idch' ] ) ? $_GET[ 'idch' ] : NULL;
	$chapitre   = verifchapitre( $link->real_escape_string( $chapitre ), $classe, $discipline );
	$eleve      = !empty( $_GET[ 'idel' ] ) ? $_GET[ 'idel' ] : NULL;
	$eleve      = verifeleve( $link->real_escape_string( $eleve ), $classe );
	if ( !empty( $classe ) ) {
		$infocl    = infocl( $classe );
		$niveau    = $infocl[ 'niveau' ];
		$nomclasse = $infocl[ 'nom' ];
	}
	echo "<div class='selecteurs'><p>";
	selectclasse( "\"moyclasse.php?idcl=\" + this.value" );
	selectdiscipline( "\"moyclasse.php?idcl=$classe&idds=\" + this.value" );
	echo "</p></div>";
	if ( !empty( $classe ) && !empty( $discipline ) ) {
		$result              = $link->query( "SELECT * FROM " . $prefix . "chapitres WHERE classe = '$classe' AND discipline = '$discipline' AND date <> '0000-00-00' AND trimestre <> 0 AND mode <> 1 ORDER BY id" );
		$chapitres           = array();
		$numschapitres       = array();
		$nomschapitres       = array();
		$dateschapitres      = array();
		$trimestreschapitres = array();
		$k                   = 0;
		while ( $ch = mysqli_fetch_array( $result ) ) {
			$chapitres[ $k ]           = $ch[ 'id' ];
			$numschapitres[ $k ]       = substr( $ch[ 'id' ], strlen( $classe ) + strlen( $discipline ) + 2 );
			$nomschapitres[ $k ]       = $ch[ 'nom' ];
			$dateschapitres[ $k ]      = $ch[ 'date' ];
			$trimestreschapitres[ $k ] = $ch[ 'trimestre' ];
			$k++;
		}
		echo "<div style='width:100%;overflow:scroll;'><table class='data'>";
		echo "<thead><tr><th rowspan='3'>El&egrave;ve</th>";
		$nbtrimchapitres = array_count_values( $trimestreschapitres );
		foreach ( $nbtrimchapitres as $k => $t ) {
			echo '<th colspan="' . $t . '">Trimestre ' . $k . '</th>';
		}
		echo "</tr><tr>";
		foreach ( $dateschapitres as $nch => $ch ) {
			echo '<th style="width:45px;">' . affdatemini( $dateschapitres[ $nch ] ) . '</th>';
		}
		echo "</tr><tr>";
		foreach ( $chapitres as $nch => $ch ) {
			echo '<th style="width:45px;"><span title="' . $nomschapitres[ $nch ] . '">' . $numschapitres[ $nch ] . '</span></th>';
		}
		echo "</tr></thead><tbody>";
		$result = $link->query( "SELECT * FROM " . $prefix . "eleves WHERE classe = '$classe' ORDER BY nom ASC, prenom ASC" );
		while ( $el = mysqli_fetch_array( $result ) ) {
			echo "<tr>";
			$tmpnom    = explode( "-", $el[ 'nom' ] );
			$nomrac    = $tmpnom[ 0 ];
			$tmpnom    = explode( "-", $el[ 'prenom' ] );
			$prenomrac = $tmpnom[ 0 ];
			echo "<td style='text-align:left;font-size:12px;'>" . $nomrac . "<br />" . $prenomrac . "</td>";
			foreach ( $chapitres as $ch ) {
				echo "<td>" . noteeleve( $classe, $discipline, $ch, $el[ 'id' ] ) . "</td>";
			}
			echo "</tr>";
		}
		foreach ( $chapitres as $ch ) {
			$lesnotesclasse[ $ch ] = notesclasse( $classe, $discipline, $ch );
		}
		echo "<tr>";
		echo "<td style='text-align:left;'><strong>MOYENNE</strong></td>";
		foreach ( $chapitres as $ch ) {
			echo "<td><strong>" . moyenne( $lesnotesclasse[ $ch ] ) . "</strong></td>";
		}
		echo "</tr>";
		echo "<tr>";
		echo "<td style='text-align:left;'><em>Effectif</em></td>";
		foreach ( $chapitres as $ch ) {
			echo "<td><em>" . effectif( $lesnotesclasse[ $ch ] ) . " / " . effectifcl( $classe ) . "</em></td>";
		}
		echo "</tr>";
		echo "<tr>";
		echo "<td style='text-align:left;'><em>Médiane</em></td>";
		foreach ( $chapitres as $ch ) {
			echo "<td><em>" . mediane( $lesnotesclasse[ $ch ] ) . "</em></td>";
		}
		echo "</tr>";
		/*echo "<tr>";
		echo "<td style='text-align:left;'><em>1e quartile</em></td>";
		foreach ( $chapitres as $ch ) {
		echo "<td><em>".q1($lesnotesclasse[$ch])."</em></td>";
		}
		echo "</tr>";
		echo "<tr>";
		echo "<td style='text-align:left;'><em>3e quartile</em></td>";
		foreach ( $chapitres as $ch ) {
		echo "<td><em>".q3($lesnotesclasse[$ch])."</em></td>";
		}
		echo "</tr>";*/
		echo "</tbody></table></div>";
	} else {
		echo "<p>Vous n'avez pas choisi soit la classe, soit la discipline.</p>";
	}
} else {
	echo "<p>Vous n'&ecirc;tes pas connect&eacute; en tant que professeur.</p>";
}
?>