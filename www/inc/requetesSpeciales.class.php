<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of requetesSpeciales
 *
 * @author jef
 */
class requetesSpeciales extends requetes{

    /*fonction qui va chercher la dernière valeure enregistrée dans la base donnees
     * pour un capteur donné. Si cet enregistrement est presque identique, on renvoie
     * false sinon on envoie true
     * on peut régler le niveau de comparaison suivant les besoins en modifiant la valeur $comparaison
     * $idCapteur : l'id du capteur que l'on veut étudier
     * $ValeurCapteur : la valeur actuelle du capteur. Valeur à enregistrée ou non
     * Valeur de retour : Vrai ou Faux suivant l'élément de comparaison*/
    public function comparerDerniereValeur($idCapteur, $ValeurCapteur){
        //selection de la dernière valeur pour le capteur $idCapteur
        $nomTable = 'donnees';
        $champsCondition = 'id_capteur';
        $valeurCondition = $idCapteur;
        $nomChamps = 'valeur_donnees';
        $tabVal = $this->RequeteSelection($nomTable, $nomChamps, $champsCondition, $valeurCondition);
        $tabVal = end($tabVal);
        
        //c'est ici que l'on peut régler le niveau comparaison entre les deux valeurs
        $niveauComparaison = 0.2;
        if($tabVal['valeur_donnees'] > $ValeurCapteur + $niveauComparaison or $tabVal['valeur_donnees'] < $ValeurCapteur - $niveauComparaison){
            return TRUE;
        }
        return FALSE;
    }
    
    /*fonction qui charger la liste de toutes les valeurs des champs date_donnees (en 
     * ne prenant que la date sans les heures) de la table donnees et supprimer les doublons
     * pas de variables d'entrée
     * valeur de retour : un tableau avec la liste des dates sans doublons */
    public function listeDateTable(){
	$nomTable = 'donnees';
        $nomChamps = "DATE_FORMAT (`date_donnees`, '%d/%m/%y')";
        $tabDatesDonnees = $this->RequeteSelection($nomTable, $nomChamps);
        $indexPlus = 0;
	for($index=0; $index<count($tabDatesDonnees)-1; $index++){
            $dateATester = $tabDatesDonnees[$index]["DATE_FORMAT (`date_donnees`, '%d/%m/%y')"];
            //La première date est automatiquement gardée            
            if ($indexPlus == 0){
		$table[$indexPlus]=$dateATester;
		$indexPlus++;
            //pour les suivantes, teste des doublons                               
            }else if ($table[$indexPlus-1]<> $dateATester){
		$table[$indexPlus]=$dateATester;
		$indexPlus++;	
            }
	}
	return $table;
}

    
    
}
