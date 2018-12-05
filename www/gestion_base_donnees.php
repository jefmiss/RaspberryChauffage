<?php
include 'ArrierePlan/configBase.inc';
//include 'ArrierePlan/functions.inc';
include 'inc/requetesbis.class.php';
include 'inc/requetesSpeciales.class.php';


/*Page qui va permettre de gérer différentes action sur la table données */
$nomFichier ='gestion_base_donnees.php';
//creation de l'objet requetes pour l'ensemble du programme et on intègre déjà le fait
//que l'on va toujours travailler sur la table donnees     
$lancementRequete = new requetesSpeciales();
$nomTable = 'donnees';

echo "<H1>GESTION DE LA BASE DONNEES (suppression - visualisation - enregistrement)</h1><BR>";


//gestion des retours du formulaire en fonction des choix qui sont transmis dans 
//$_GET['action']
if (isset($_GET['action'])){
    switch ($_GET['action']){
	case "supptout" ://suppression de tous les enregistrements
            $lancementRequete->requeteSuppression($nomTable);
            echo "toutes les données ont été supprimées<BR>";
            break;
        case "suppuncapteur" ://suppression d'un seul capteur
            //on va chercher dans la base les différents capteurs
            $nomTable = 'capteurs';
            $nomChamps = 'id_capteur';
            $tableIdCapteurs = $lancementRequete->RequeteSelection($nomTable, $nomChamps);
            //on réaffecte la valeur nomTable avec la variable par défaut
            $nomTable = 'donnees';
            //on créé le formulaire ou une liste déroulante permettra de choisir le capteur
            echo "<form method='GET' action='$nomFichier'>";
            echo "<SELECT name=idCapteur>";
            //on va introduire chaque idCapteur en option dans la liste
            foreach ($tableIdCapteurs as $keyId => $valueId) {
                echo "<OPTION>". $valueId['id_capteur'];"</OPTION>";    
            }
            echo "</SELECT>";
            echo "<input type='submit' name='action' value='Capteur choisi'>";
            echo "</form>";		
            break;
        case "enregistre" ://réalisaation d'un enregistrement manuel dans la table données
            //recherche les infos dans la table capteur qui est mise à jour régulièrement
            $nomTable = 'capteurs';
            $tabCapteurs = $lancementRequete->RequeteSelection($nomTable);
            //on réaffecte la valeur nomTable avec la variable par défaut
            $nomTable = 'donnees';
            //on enregistre les infos importantes dans la table donnees
            foreach ($tabCapteurs as $keyCapteur => $valueCapteur) {
                //modification de la valeurCapteur pour en faire un nombre dans la base    
                $valeurCapteur = $valueCapteur['valeur_capteur'];
                $valeurCapteur = str_replace("°C","",$valeurCapteur);
                $valeurCapteur = strval(str_replace(",",".",$valeurCapteur));
                //on test avec la fonction comparerDerniereValeur si la valeur actuelle 
                //est plus ou moins inférieur à un niveau de comparaison
                //si vrai on enregistre, sinon on ne fait rien
                $enregistre = $lancementRequete->comparerDerniereValeur($valueCapteur['id_capteur'], $valeurCapteur);
                if ($enregistre){
                    $nomChamps = array('id_capteur', 'valeur_donnees');
                    $valeurChamps = array($valueCapteur['id_capteur'],$valeurCapteur);
                    $lancementRequete->RequeteAjout($nomTable, $nomChamps, $valeurChamps);
                }
            }
            echo "de nouveaux enregistrement ont été fait !";
            Break;
        case "Capteur choisi" : //retour du formulaire de suppression d'un capteur avec l'id capteur choisi
            $champsCondition = 'id_capteur';
            $valeurCondition = $_GET['idCapteur'];
            $lancementRequete->requeteSuppression($nomTable, $champsCondition, $valeurCondition);
            echo "les donnees du capteur ".$_GET['idCapteur']." ont été supprimées";
            break;
        
        case "voirtempjour" : //retour sur la page avec un formulaire qui présente toutes les dates dans la base données
            //on va choisir la date du jour que l'on veut choisir
            //on créé un formulaire avec toutes les dates existentes dans la base
            echo "<form method='GET' action='$nomFichier'>";
            echo "<SELECT name=dateJour>";
            //on envoi sur une fonction qui va nous retourner toutes les dates sans doublons enregistrées dans la base
            $tabDate = $lancementRequete->listeDateTable();
            for ($index=0; $index<count($tabDate); $index++){
		echo "<OPTION>".$tabDate[$index];
            }
            echo "</SELECT>";
            echo "<input type='submit' name='action' value='Date choisie'>";
            echo "</form>";
            break;
			
	case "Date choisie" ://traitement du formulaire sur la selection de la date
            //pour l'instant cette partie reste à faire
            echo "<BR>la date choisie est ".$_GET['dateJour'];
            break;
    }
}

//formulaire pour faire des actions sur la base
echo "<BR><form metho='GET' action='test.php'>";
echo "<BR><a href='$nomFichier?action=enregistre'>Enregistre de nouvelles données</a>";
echo "<BR><a href='$nomFichier?action=supptout'>Effacer toute la base</a>";
echo "<BR><a href='$nomFichier?action=suppuncapteur'>Effacer les donnees d'un capteur</a>";
echo "<BR><a href='$nomFichier?action=voirtempjour'>voir les temperatures d'un jour</a>";
echo "</form>";