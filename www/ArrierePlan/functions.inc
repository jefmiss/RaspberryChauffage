<?php
$nomHote ="localhost";
$nomUser = "raspberryPi";
$passWord = "raspberryMiss";
$nomBase = "pi";

//$capteurs = array("28-000004e4aca0"=>"Temperature Sortie Chaudiere",
//                  "28-000004e4bf59"=>"Temperature Ambiante Cuisine",
//                  "28-000004e48bd2"=>"Temperature Entree Radiateurs");


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

function recupererAffichage( $nom )
{
        $sql = "SELECT valeur FROM affichage WHERE nom='".$nom."'";
	$result = mysqli_query( $sql );
	if ($row = mysqli_fetch_array($result, MYSQL_NUM))
	{
		return $row[0];
	}
	return "";
}

//fonction pour enregistrer dans la table affichage, des nouvelles valeurs avec
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
    $sql = "SELECT * FROM affichage WHERE id_capteur='".$idCapteur."'";
    $result = $mydb->query($sql );
    //test du résultat
    if($result->rowCount()){
        $sql = "UPDATE affichage SET valeur_affichage='".$valeur."' WHERE id_capteur='".$idCapteur."'";
    }else{
        $sql = "INSERT INTO affichage ( id_capteur, valeur_affichage ) VALUES ('".$idCapteur."','".$valeur."')";
    }        
    //lancement de la requete de mise à jour ou d'insertion
    $mydb->query($sql);
    
}


?>