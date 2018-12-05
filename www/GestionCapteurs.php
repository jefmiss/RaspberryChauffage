
<?php
include 'ArrierePlan/configBase.inc';
include 'ArrierePlan/functions.inc';
include 'inc/requetesbis.class.php';
echo "CAPTEURS ENRGISTRÉS DANS LA BASE ACTUELLE";
//programme test pour gérer la base des capteurs : enregistrer, modifier, supprimer des capteurs

//creation de l'objet requetes pour l'ensemble du programme 
$lancementRequete = new requetes();
$nomTable ='capteurs';

//si le lien supprimer a été activé on supprime le capteur selectionné
if (isset($_GET['action']) and $_GET['action']=="supp"){
    $champsCondition = 'id_capteur';
    $valeurCondition = $_GET['id-capteur'];
    $lancementRequete->requeteSuppression($nomTable, $champsCondition, $valeurCondition);
    }

//si on arrive sur la page avec un bouton modifier on modifie le capteur sélectionné
if (isset($_GET['valider'])){
    $nomChamps = array('designation_capteur', 'code_capteur');
    $valeurChamps = array($_GET['designation-capteur'], $_GET['code-capteur']);
    $champsCondition = 'id_capteur';
    $valeurCondition = $_GET['id-capteur'];
    $lancementRequete->RequeteModification($nomTable, $nomChamps, $valeurChamps, $champsCondition, $valeurCondition);
}

//si la demande enregistrer nouveau capteur faite on enregistre les capteurs nouveaux dans la base
if (isset($_GET['nouveau'])){
    //on charge un tableau avec les capteurs présents dans le raspberry et un tableau
    //avec les capteurs déjà enregistrés dans la base
    $tabIdCapteur = recupererIdCapteurRaspPi();
    $tabCapteurs = $lancementRequete->RequeteSelection($nomTable);
    //pour chaque capteur présent dans le rapsberry on vérifie si il existe dans la base
    //si ce n'est pas le cas on les rajoute
    for ($index = 0; $index<count($tabIdCapteur); $index++){
        $ajout = true;
        for ($indexBis = 0; $indexBis < count($tabCapteurs); $indexBis++){
            if($tabIdCapteur[$index] == $tabCapteurs[$indexBis]['id_capteur']){
                $ajout = false;
                break;
            }
        }
        if ($ajout) {
            $nomChamps = 'id_capteur';
            $valeurChamps = $tabIdCapteur[$index];
            $lancementRequete->RequeteAjout($nomTable, $nomChamps, $valeurChamps);            
        }
    }
}
   
//charger la base existante des capteurs et la mettre dans un tableau 
$tabCapteurs = $lancementRequete->RequeteSelection($nomTable);
//    echo '<pre>';        
//    print_r($tabCapteurs);
//    echo '</pre>';

//création du tableau avec l'ensemble des capteurs
echo "<Table border='1'>"
        . "<TR>"
        . "<TH>Id Capteur</TH>"
        . "<TH>Nom Capteur</TH>"
        . "<TH>Code Capteur</TH>"
        . "<TH>Valeur temperature</TH></TR>";

//chaque ligne est écrite avec cette boucle et un test est fait si une modification est prévue
for ($index = 0; $index < count($tabCapteurs); $index++)
{
    if (isset($_GET['action']) and $_GET['action']=="modif" and  $_GET['id-capteur']==$tabCapteurs[$index]['id_capteur'])
    {
        echo "<form method='get' action='GestionCapteurs.php'>";
        echo "<TR><TD>".$tabCapteurs[$index]['id_capteur']."</TD>"
                . "<TD><input type='text' name='designation-capteur' value='".$tabCapteurs[$index]['designation_capteur']."'</TD>"
                . "<TD><input type='text' name='code-capteur' value='".$tabCapteurs[$index]['code_capteur']."'</TD>"
                . "<TD>".$tabCapteurs[$index]['valeur_capteur']."</TD>"
                . "<TD colspan=2><input type='submit' name='valider' value='modifier'><input type='hidden' name='id-capteur' value='".$tabCapteurs[$index]['id_capteur']."'</TD></TR>";
        echo "</form>";
    }else
    {
	echo "<TR><TD>".$tabCapteurs[$index]['id_capteur']."</TD>"
                . "<TD>".$tabCapteurs[$index]['designation_capteur']."</TD>"
                . "<TD>".$tabCapteurs[$index]['code_capteur']."</TD>"
                . "<TD>".$tabCapteurs[$index]['valeur_capteur']."</TD>"
                . "<TD><a href='GestionCapteurs.php?id-capteur=".$tabCapteurs[$index]['id_capteur']."&action=modif'>modifier</a></TD>"
                . "<TD><a href='GestionCapteurs.php?id-capteur=".$tabCapteurs[$index]['id_capteur']."&action=supp'>supprimer</a></TD></TR>";
    }
}
echo "</table>";

//si on n'est pas en mode modification, on créé un formulaire pour la création de capteurs
echo "<form method='get' action='GestionCapteurs.php'>";
echo "<input type='submit' name='nouveau' value='rajouter les capteurs du Rsapberry'>";
echo "</form>";
?>