#!/usr/bin/php
<?php
include 'configBase.inc';
include 'functions.inc';
include __DIR__.'/../inc/requetesbis.class.php';
include __DIR__.'/../inc/requetesSpeciales.class.php';

//creation de l'objet requetes pour l'ensemble du programme 
$lancementRequete = new requetesSpeciales();

//on va chercher les infos dans la table capteur qui est mise à jour régulièrement
$tableCapteurs = $lancementRequete->RequeteSelection('capteurs');			

//les informations de chaque capteur seront enregistrées dans la table données
//on rajoute une fonction qui permet de comparer la valeur actuelle avec la
//valeur précédente et on enregistre uniquement si les écarts sont nécessaires
//le niveau de comparaison est défini dans la fonction comparerDerniereValeur
for ($index=0; $index<count($tableCapteurs); $index++){
				
    //modification de la valeurCapteur pour en faire un nombre dans la base
    $valeurCapteur = $tableCapteurs[$index]['valeur_capteur'];
    $valeurCapteur = str_replace("°C","",$valeurCapteur);
    if ($valeurCapteur <> "NaN"){
        $valeurCapteur = strval(str_replace(",",".",$valeurCapteur));
        $enregistre = $lancementRequete->comparerDerniereValeur($tableCapteurs[$index]['id_capteur'], $valeurCapteur);
								
        //avec la requete ajout on envoi un fichier avec les données à enregistrer
        //Attention la date n'est pas envoyé ici, mais on utilise la fonction NOW() dans la commande SQL
        $tabDonnees = array($tableCapteurs[$index]['id_capteur'], $valeurCapteur);
        if ($enregistre){
            $nomTable = 'donnees';
            $nomChamps = array('id_capteur', 'valeur_donnees');
            $valeurChamps = array($tableCapteurs[$index]['id_capteur'], $valeurCapteur);
            $lancementRequete->RequeteAjout($nomTable, $nomChamps, $valeurChamps);
        }
    }
}
			
?>