<?php
include 'ArrierePlan/configBase.inc';
include 'ArrierePlan/functions.inc';
include 'inc/requetesbis.class.php';
include 'inc/requetesSpeciales.class.php';

echo "<h1> PAGE DE TEST DE LA CLASS REQUETE BIS</h1><BR>";

$testRequete = new requetes();

//test d'une selection d'une table
$nomTable = array('affichage');
$nomChamps = 'valeur_affichage';
$valeurChamps = 200;
$champsCondition = 'id_affichage';
$valeurCondition = 7;

$tab = $testRequete->requeteSuppression($nomTable, $champsCondition, $valeurCondition);

echo $tab;
//echo '<pre>';
//print_r($tab);
//echo'</pre>';
