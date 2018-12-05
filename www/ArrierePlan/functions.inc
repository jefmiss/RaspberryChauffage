<?php

//fonction de connexion à la base à partir de parametres définis en début du fichier
function connexionBase(){
   global $nomHote, $nomUser, $passWord,$nomBase;
    global $mydb;
    $dsn = "mysql:host=$nomHote;dbname=$nomBase;port=22;charset=UTF8";
    try{
        $mydb = new PDO($dsn,$nomUser,$passWord, array(PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION));
        //echo "on est bien passer par la connexion et ça marche<BR>";
    } catch (Exception $ex) {
        die('Erreur :'. $ex->getMessage());
    }
}

//fonction qui va chercher la dernière valeure enregistrée dans la base
//pour un capteur donné. Si cette enregistrement est presque identique, on renvoie
//false sinon on envoie true
//on peut régler la comparaison suivant les besoins
//function comparerDerniereValeur($idCapteur, $ValeurCapteur){
//    connexionBase();//envoie de la fonction pour se connecter
//    global $mydb;//valeur correspondant à cette connexion
//	
//        //selection de la dernière valeur pour ce capteur
//	$sql = "SELECT `valeur_donnees` FROM `donnees` WHERE `id_capteur`='".$idCapteur."' ORDER BY `id_donnees` DESC LIMIT 1";
//	$result = $mydb->query($sql);
//	$tabVal = $result->fetch(PDO::FETCH_ASSOC);
//        
//        //echo "pour le capteur $idCapteur la température actuelle est de $ValeurCapteur alors que dans la base la température est de "
//        //        .$tabVal['valeur_donnees'];
//        //c'est ici que l'on peut régler le niveau comparaison entre les deux valeurs
//        $niveauComparaison = 0.2;
//        if($tabVal['valeur_donnees'] > $ValeurCapteur + $niveauComparaison or $tabVal['valeur_donnees'] < $ValeurCapteur - $niveauComparaison){
//        //    echo " on peut donc enregistrer ce capteur <BR>";
//            return TRUE;
//        }
//        //echo " on ne peut pas enregistrer ce capteur <BR>";
//        return FALSE;
//}

function lectureCapteurDS18B20($idCapteur){
    $return = NAN;
    $nomFichier = "/sys/bus/w1/devices/$idCapteur/w1_slave";
    if (file_exists($nomFichier))
    {
        $contenuFichier = file($nomFichier);
        if (count($contenuFichier) == 2) {
            if (preg_match("/YES *$/", $contenuFichier[0])) {
                $contenuFichier = preg_replace( "/^.*t=([0-9]+) *$/", "$1", $contenuFichier[1]);
                $return = floatval($contenuFichier)/1000;
            }
        }
    }
    return $return;
}

//fonction qui renvoie les idCapteur présent dans le répertoire actuel du raspberry
function recupererIdCapteurRaspPi(){
    $nomRepertoire = "/sys/bus/w1/devices/";
    $index = 0;
    if ($handle = opendir($nomRepertoire)){
        while (FALSE !== ($entry = readdir($handle))){
            if (substr($entry, 0, 2)== 28){
                $tabIdRepertoire[$index] =  $entry;
                $index++;
            }
        }
    }
    return $tabIdRepertoire;
}

function recupererAffichage( $idCapteur )
{
    connexionBase();//envoie de la fonction pour se connecter
    global $mydb;//valeur correspondant à cette connexion
    $sql = "";
	$sql = "SELECT valeur_affichage FROM affichage WHERE id_capteur='$idCapteur'";
//echo $sql;

    $result = $mydb->query($sql);
    $tabVal = $result->fetch(PDO::FETCH_ASSOC);
    return $tabVal['valeur_affichage'];
//    echo '<pre>';        
//    print_r($tabVal);
//    echo '</pre>';

}

//fonction pour enregistrer dans la table capteur, des nouvelles valeurs avec
//un test pour soit une mise à jour si le capteur existe ou une insertion pour
//un nouveau capteur.
function transmettreAffichage( $idCapteur, $valeur )
{
    connexionBase();//envoie de la fonction pour se connecter
    global $mydb;//valeur correspondant à cette connexion
    
    //on sélectionne une ligne avec l'id du capteur. Si la requete envoie
    //une ligne, le capteur existe et on met les informations à jour avec la 
    //nouvelle valeur, sinon, le capteur n'existe par et il faut le créer dans
    //la base
    $sql = "SELECT * FROM capteurs WHERE id_capteur='".$idCapteur."'";
    $result = $mydb->query($sql);
    //test du résultat
    if($result->rowCount()){
        $sql = "UPDATE capteurs SET valeur_capteur='".$valeur."' WHERE id_capteur='".$idCapteur."'";
    }else{
        $sql = "INSERT INTO capteurs ( id_capteur, valeur_capteur ) VALUES ('".$idCapteur."','".$valeur."')";
    }
    //lancement de la requete de mise à jour ou d'insertion
    $mydb->query($sql);
    
}

//fonction pour sélectionner l'ensemble de la base des capteurs et envoyer un tableau avec tous les résultats
function selectionnerCapteurs(){
    connexionBase();//envoie de la fonction pour se connecter
    global $mydb;//valeur correspondant à cette connexion

	$sql = "SELECT * FROM capteurs";
	$result = $mydb->query($sql);
	$indexTab = 0;
	
	while ($tabVal[$indexTab] = $result->fetch(PDO::FETCH_ASSOC)){
		$indexTab = $indexTab+1;
	}
	
    //echo '<pre>';        
    //print_r($tabVal);
    //echo '</pre>';
	
	return $tabVal;
}

//fonction qui récupère l'ensemble des valeurs de la table donnees
function selectionnerDateDonnees(){
    connexionBase();//envoie de la fonction pour se connecter
    global $mydb;//valeur correspondant à cette connexion

	$sql = "SELECT DATE_FORMAT(`date_donnees`,'%d/%m/%y') FROM `donnees` WHERE 1 ";
	$result = $mydb->query($sql);
	$indexTab = 0;
	
	while ($tabVal[$indexTab] = $result->fetch(PDO::FETCH_ASSOC)){
		$indexTab = $indexTab+1;
	}
	
    //echo '<pre>';        
    //print_r($tabVal);
    //echo '</pre>';
	
	return $tabVal;
}

//fonction qui renvoit un tableau avec tous les IdCapteurs de la table capteur
//cette fonction utilise le retour de la fonction selectionnerCapteurs
function selectionnerIdCapteurs(){
    $tabCapteurs = selectionnerCapteurs();
    for ($index=0; $index<count($tabCapteurs)-1; $index++){
        $tabIdCapteur[$index] = $tabCapteurs[$index]['id_capteur'];
    }
    return $tabIdCapteur;
}

//fonction pour ajouter un capteur dans la base à partir de son id_cpateur
//attention lors de l'ajout seule le champ id_capteur est complété. Pour le reste
//il faudra modifier le capteur pour compléter
function ajouterCapteur($idCapteur){
    connexionBase();//envoie de la fonction pour se connecter
    global $mydb;//valeur correspondant à cette connexion

    $sql = "INSERT INTO capteurs (id_capteur) VALUES ('$idCapteur')";
    echo $sql;
    $mydb->query($sql);
}

//fonction pour ajouter des données enregistrées dans la base capteur
function ajouterDonnees($tabDonnees){
    connexionBase();//envoie de la fonction pour se connecter
    global $mydb;//valeur correspondant à cette connexion
    
    $sql = "INSERT INTO donnees (id_capteur, date_donnees, valeur_donnees) VALUES ('"
	.$tabDonnees[0]."', "
	."NOW(), "
	.$tabDonnees[1].")";
    //echo $sql;
    $mydb->query($sql);
}

//fonction pour supprimer tout ou partie des données de la base donnees
function supprimerDonnees($critere, $valeurs = NULL){
    connexionBase();//envoie de la fonction pour se connecter
    global $mydb;//valeur correspondant à cette connexion

	switch ($critere){
		case "tout":
			$sql = "DELETE FROM donnees";
			//echo $sql;
			$mydb->query($sql);
			break;
		case "unCapteur":
			$sql = "DELETE FROM donnees WHERE id_capteur = '".$valeurs;
			echo $sql;
			//$mydb->query($sql);
			break;
	}
}

//fonction pour supprimer un capteur de la base en fonction de son id_capteur
function supprimerCapteur($idCapteur){
    connexionBase();//envoie de la fonction pour se connecter
    global $mydb;//valeur correspondant à cette connexion

    $sql = "DELETE FROM capteurs WHERE id_capteur='".$idCapteur."'";
    $mydb->query($sql);
}

//function pour modifier un capteur de la base en fonction de son id_capteur
// attention : impossible de modifier l'id_capteur
function modifierCapteur($idCapteur, $tabValCapteur){
    connexionBase();//envoie de la fonction pour se connecter
    global $mydb;//valeur correspondant à cette connexion

    $sql = "UPDATE capteurs SET ";
    $sql = $sql."designation_capteur='".$tabValCapteur[0]."'";
    $sql = $sql.", code_capteur='".$tabValCapteur[1]."'";
    $sql = $sql." WHERE id_capteur='".$idCapteur."'";
    $mydb->query($sql);
}

/*--------------------LANCEMENT D'UNE REQUETE SELECTION----------------
fonction qui lance une requete standard de selection avec les possibilités de 
conditions de classement, ...
Variables d'entrée voir les noms explicites avec possibilité d'avoir une variable
ou plusieurs (sous forme de tableau). Attention condition simple champ=valeur
Retourne un tableau avec toutes les valeurs */
function RequeteSelection($NomTable, $NomChamps, $ChampsCondition=false, $ValeurCondition=false, $ChampsClassement=false){
    connexionBase();//envoie de la fonction pour se connecter
    global $mydb;//valeur correspondant à cette connexion

    //inserer des champs specifiques a selectionner
    $NomChamps = (array) $NomChamps;
    $Champs = $NomChamps[0];
    for ($I=1; $I<count($NomChamps); $I++){
        $Champs = $Champs . ", " . $NomChamps[$I];
    }
    $Requetes = "SELECT " . $Champs;

    //inserer la ou les tables a selectionner
    $NomTable = (array) $NomTable;
    $Table = $NomTable[0];
    for ($I=1; $I<count($NomTable); $I++){
        $Table = $Table . " NATURAL JOIN " . $NomTable[$I];
    }
    $Requetes = $Requetes . " FROM " . $Table;


    //inserer les conditions de selection
    if($ChampsCondition){
        $ChampsCondition = (array) $ChampsCondition;
        $ValeurCondition = (array) $ValeurCondition;
        $Condition = $ChampsCondition[0] . "= \"" . $ValeurCondition[0]."\"";
        for ($I=1; $I<count($ChampsCondition); $I++){
            $Condition = $Condition . " AND " . $ChampsCondition[$I] . "= \"" . $ValeurCondition[$I]."\"";
        }
        $Requetes = $Requetes . " WHERE " . $Condition;
    }

    //inserer le champs de classement
    if($ChampsClassement){
        $ChampsClassement = (array) $ChampsClassement;
        $Classement = $ChampsClassement[0];
        for ($I=1; $I<count($ChampsClassement); $I++){
            $Classement = $Classement . ", " . $ChampsClassement[$I];
        }
        $Requetes = $Requetes . " ORDER BY " . $Classement;
    }

    //envoyer la requetes
    //echo "<BR>".$Requetes."<BR>";
    $result = $mydb->query($Requetes);
    
    //transformer la variable de retour dans un tableau
    $indexTab = 0;
    while ($TableResultats[$indexTab] = $result->fetch(PDO::FETCH_ASSOC)){
        $indexTab++;
    }
    unset($TableResultats[$indexTab]);
            
    return $TableResultats;
}

?>