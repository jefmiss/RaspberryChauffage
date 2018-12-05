#!/usr/bin/php
<?php
include 'configBase.inc';
include 'functions.inc';


/*Ce programme va charger tout les idcapteur présent <sur le Raspberry - A SUPPRIMER> 
 * <sur la table capteur A CONSERVER>. 
 * il va chercher pour chaque capteur la valeur de la température sur le Raspberry
 * il l'a transforme au bont format 
 * il enregistre cette valeur dans la table capteur
 */

/*Recherche de tous les idcapteur connecté sur le rapsberry
Cette partie est supprimée pour partir plutôt de la table capteur de la base
plutot que des capteurs présent dans le raspberry comme cela si un capteurs est
défectueux on lui donne la valeur de NAN - lignes à supprimer si tout est ok - nov2018
$tabIdCapteur = recupererIdCapteurRaspPi();
 */

//chercher les idCapteur présent dans la table Capteur
$tabIdCapteur = selectionnerIdCapteurs();

//pour chaque capteur on va lire la température et l'enregistrée dans la table capteur
for ($index=0; $index<count($tabIdCapteur); $index++){
    $idCapteur = $tabIdCapteur[$index];
    $contenuFichier=sprintf("%.1f°C", lectureCapteurDS18B20($idCapteur));
    $contenuFichier=str_replace(".",",",$contenuFichier);
    echo "id capteur = $idCapteur et la valeur est égale à $contenuFichier<BR>";
    transmettreAffichage( $idCapteur, $contenuFichier);
}
?>
