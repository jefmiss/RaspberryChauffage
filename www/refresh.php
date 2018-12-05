<?php
include 'ArrierePlan/configBase.inc';
include 'ArrierePlan/functions.inc';
include 'inc/requetesbis.class.php';
echo "<link rel='stylesheet' href='css/test.css'\>";

/*page php qui est la partie qui se modifie toutes les secondes sur la page index*/

//creation de l'objet requetes pour l'ensemble du programme 
$lancementRequete = new requetes();

//on va chercher les infos dans la table capteurs
$tableCapteurs = $lancementRequete->RequeteSelection('capteurs');

//on inscrit la date et l'heure histoire de voir si ca tourne bien
$date = date("d-m-Y");
$heure = date("H:m:s");

// pour chaque capteur on va inscrire les informations
//on définit des formats particuliers en fonction de la feuille de style
foreach ($tableCapteurs as $keyCapteurs => $valueCapteurs) {
    $styleCouleur =  couleurTemp($valueCapteurs['valeur_capteur']);
    echo "<div class=".$valueCapteurs['code_capteur'].">";
    echo "<span class=".$styleCouleur.">".$valueCapteurs['valeur_capteur']."</span></div><BR>";
}

echo "<img src='img/synoptique.jpg' alt='ma_photo' \>";
	
//A partir d'ici, il s'agit de tester si le bouton poussoir est appuyé si c'est
//le cas, on créé un fichier shutdown dans le répertoire indiqué qui sera ensuite
//repéré par le programme launcher.sh et qui entraînera la fermeture du raspberry

exec ( "gpio read 6", $status );
    // echo $status[0]."<BR>";
	if ($status[0]==1) {
		echo "le bouton n'est pas appuyé<BR>";
	}else {
		echo "le bouton est appuyé";
		exec("touch /home/pi/projects/chauffage/shutdown");
	}
 
function couleurTemp($valeurTemp)
{
	$valeurTemp = strval(substr($valeurTemp,0,2));
	if ($valeurTemp<25){return "blue";}
	if ($valeurTemp>37){return "red";}
	return "orange";
}
?>
