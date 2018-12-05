<?php


/**
 * l'objet de cette classe est de créer rapidement des requetes en lien avec la 
 * la base de données dont les éléments sont décrits ici
 * @author jef
 */
class requetes {

    // valeur de connexion à la base
    private $nomHote = "localhost";
    private $nomUser = "raspberryPi";
    private $passWord = "raspberryMiss";
    private $nomBase = "pi";
    public $mydb;
    //variable standart pour les requetes
//    public $nomTable;
//    public $nomChamps = array('*');
//    public $valeurChamps = array("");
//    public $champsCondition = false;
//    public $valeurCondition = '';
//    public $champsClassement = false;

    /* LANCEMENT DE LA FONCTION DE CONNEXION À LA BASE
     * fonction qui se lance uniquement à partir de la class pour ce connecter à la base
     * dont les données sont définies dans les variables au départ de la class
     */
    private function connexionBase() {
        $dsn = "mysql:host=$this->nomHote;dbname=$this->nomBase;port=22;charset=UTF8";
        try {
            $this->mydb = new PDO($dsn, $this->nomUser, $this->passWord, array(PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION));
        } 
        catch (Exception $ex) {
            die('Erreur :' . $ex->getMessage());
        }
    }

    /* LANCEMENT DE LA REQUETE
     * fonction qui se lance uniquement à partir de la class. Les fonctions de la class définissent 
     * la requete, lance cette fonction qui retourne un tableau associatif avec les valeurs demandées 
     * C'est uniquement cette fonction qui lance la connexion à la base.
     */
    protected function lancementRequetes($requetes, $typeRequete = "SELECT") {
        $this->connexionBase(); //envoie de la fonction pour se connecter
        //envoyer la requetes
        //echo "<BR>" . $requetes . "<BR>";
        $result = $this->mydb->query($requetes);

        //Si besoin transformer la variable de retour dans un tableau
        //sinon ne rien renvoyer
        if ($typeRequete == "SELECT" or $typeRequete == "SHOW_COLUMNS") {
            $indexTab = 0;
            while ($tableResultats[$indexTab] = $result->fetch(PDO::FETCH_ASSOC)) {
                $indexTab++;
            }
            unset($tableResultats[$indexTab]);
            return $tableResultats;
        } else if ($typeRequete == "INSERT") {
            $dernierId = $this->mydb->lastInsertId();
            return $dernierId;
        } else if ($typeRequete == 'DELETE'){
            return $result;
        }
    }

    /* --------------------LANCEMENT D'UNE REQUETE SELECTION SIMPLE----------------
     * fonction qui lance une requete de selection simple avec les possibilités de
     * conditions de classement, ...
     * $nomTable : si plusieurs tables => SELECT... NATURAL JOIN... 
     * $nomChamps : par défaut * sinon un ou plusieurs champs
     * $champsCondition : si plusieurs champs => opérateur AND pour les conditions
     * $valeurCondition : ogligatoirement le même nombre que $ChampsConditions
     * $champsClassement : RAS
     * Retourne un tableau avec 1 tableau par enregistrement qui reprend les champs sélectionner */
    function RequeteSelection($nomTable, $nomChamps='*', $champsCondition=false, $valeurCondition='', $champsClassement=false) {
        //inserer des champs specifiques a selectionner
        $nomChamps = (array) $nomChamps;
        $Champs = $nomChamps[0];
        for ($i = 1; $i < count($nomChamps); $i++) {
            $Champs = $Champs . ", " . $nomChamps[$i];
        }
        $requetes = "SELECT " . $Champs;

        //inserer la ou les tables a selectionner
        $nomTable = (array) $nomTable;
        $table = $nomTable[0];
        for ($i = 1; $i < count($nomTable); $i++) {
            $table = $table . " NATURAL JOIN " . $nomTable[$i];
        }
        $requetes = $requetes . " FROM " . $table;

        //inserer les conditions de selection
            if ($champsCondition) {
            $champsCondition = (array) $champsCondition;
            $valeurCondition = (array) $valeurCondition;
            $Condition = $champsCondition[0] . "= \"" . $valeurCondition[0] . "\"";
            for ($i = 1; $i < count($champsCondition); $i++) {
                $Condition = $Condition . " AND " . $champsCondition[$i] . "= \"" . $valeurCondition[$i] . "\"";
            }
            $requetes = $requetes . " WHERE " . $Condition;
        }

        //inserer le champs de classement
        if ($champsClassement) {
            $champsClassement = (array) $champsClassement;
            $Classement = $champsClassement[0];
            for ($i = 1; $i < count($champsClassement); $i++) {
                $Classement = $Classement . ", " . $champsClassement[$i];
            }
            $requetes = $requetes . " ORDER BY " . $Classement;
        }

        //lancement de la fonction de connexion et query qui nous retourne un tableau avec les valeurs
        $resultats = $this->lancementRequetes($requetes);
        return $resultats;
    }

    /* --------------------LANCEMENT D'UNE REQUETE MODIFICATION ENREGISTREMENT----------------
     * fonction qui lance la requete standard de modification d'enregistrement avec les possibilités
     * de conditions. 
     * $nomTable : pour l'instant si plusieurs tables => seule la première est retenue 
     * $nomChamps : le champs ou les (tableaux) à modifier
     * $valeurChamps : même nombre que nomChamps
     * $champsCondition : Attention si non précisé tous les champs sont modifiés
     *                    si plusieurs champs => opérateur AND pour les conditions
     * $valeurCondition : ogligatoirement le même nombre que $ChampsConditions
     * Retourne un tableau avec 1 tableau par enregistrement qui reprend les champs sélectionner */
    function RequeteModification($nomTable, $nomChamps, $valeurChamps, $champsCondition=false, $valeurCondition='') {
        //inserer la table a selectionner
        $nomTable = (array) $nomTable;
        $table = $nomTable[0];
        $requetes = "UPDATE " . $table;

        //inserer les champs a modifier et leur valeur
        $nomChamps = (array) $nomChamps;
        $valeurChamps = (array) $valeurChamps;
        $Champs = $nomChamps[0] . " = \"" . $valeurChamps[0] . "\"";
        for ($i = 1; $i < count($nomChamps); $i++) {
            $Champs = $Champs . ", " . $nomChamps[$i] . " = \"" . $valeurChamps[$i] . "\"";
        }
        $requetes = $requetes . " SET " . $Champs;

        //inserer les conditions de selection
        if ($champsCondition){
            $champsCondition = (array) $champsCondition;
            $valeurCondition = (array) $valeurCondition;
            $Condition = $champsCondition[0] . "=\"" . $valeurCondition[0] . "\"";
            for ($i = 1; $i < count($champsCondition); $i++) {
                $Condition = $Condition . " AND " . $champsCondition[$i] . "=\"" . $valeurCondition[$i] . "\"";
            }
            $requetes = $requetes . " WHERE " . $Condition;
        }
        //envoyer la requetes
        //lancement de la fonction de connexion et query qui nous retourne un tableau avec les valeurs
        $this->lancementRequetes($requetes, "UPDATE");
    }

    /* --------------------LANCEMENT D'UNE REQUETE AJOUT----------------
     * fonction qui lance L'ajout d'un enregistrement
     * $nomTable : si plusieurs tables => seule la première est retenue 
     * $nomChamps : soit vide => tous les champs de la table sont chargés
     *              soit les champs précisé seront ajoutés 
     * $valeurChamps : même nombre que nomChamps
     * Retourne le numéro de l'ID du nouvel enregistrement */
    public function RequeteAjout($nomTable, $nomChamps, $valeurChamps) {
        //récupéraiton d'un tableau avec les colonne de la table

        //Ici une seule table possible donc la variable $table est simple
        $nomTable = (array) $nomTable;
        $table = $nomTable[0];
        $requetes = "INSERT INTO " . $table;
        
        //inserer les champs de la table
        //si la variable nonChamps est vide, charge tous les champs de la table
        //le premier champs d'une table est toujours un AUTOINCREMENT donc n'est
        //pas traité
        if ($nomChamps==""){
            $nomChamps = $this->listeChampsTable($nomTable);
            array_shift($nomChamps);//on supprime le premier champs AUTOINCREMENT
        } else {
            $nomChamps = (array)$nomChamps;
        }
        $Champs = $nomChamps[0];
        for ($i = 1; $i < count($nomChamps); $i++) {
            $Champs = $Champs . ", " . $nomChamps[$i];
        }
        $requetes = $requetes . " (" . $Champs . ")";

        //inserer les valeurs des champs
        $valeurChamps = (array) $valeurChamps;
        $Valeurs = " \"" . $valeurChamps[0] . "\"";
        for ($i = 1; $i < count($valeurChamps); $i++) {
            $Valeurs = $Valeurs . ", \"" . $valeurChamps[$i] . "\"";
        }
        $requetes = $requetes . " VALUES (" . $Valeurs . ") ";

        //envoyer la requetes
        //lancement de la fonction de connexion et query qui nous retourne la valeur de l'id
        //du nouvel enregistrement
        $numId = $this->lancementRequetes($requetes, "INSERT");
        return $numId;
    }

    /* --------------------LANCEMENT D'UNE REQUETE SUPPRESSION----------------
     * fonction qui lance la suppression d'un ou plusieurs enregistrements
      * $nomTable : si plusieurs tables => seule la première est retenue 
     * $nomChamps : attention si vide, tous les enregistrements de la table sont supprimés 
     * $valeurChamps : même nombre que nomChamps
     * Retourne rien */

    function requeteSuppression($nomTable, $champsCondition, $valeurCondition) {
        //Ici une seule table possible donc la variable $table est simple
        $nomTable = (array) $nomTable;
        $table = $nomTable[0];
        $requetes = "DELETE FROM " . $table;

        //inserer les conditions de suppression si ils existent
        if ($champsCondition){
            $champsCondition = (array) $champsCondition;
            $valeurCondition = (array) $valeurCondition;
            $Condition = $champsCondition[0] . "=\"" . $valeurCondition[0] . "\"";
            for ($i = 1; $i < count($champsCondition); $i++) {
                $Condition = $Condition . " AND " . $champsCondition[$i] . "=\"" . $valeurCondition[$i] . "\"";
            }
            $requetes = $requetes . " WHERE " . $Condition;
        }

        //envoyer la requetes
        //lancement de la fonction de connexion et query qui nous retourne un tableau avec les valeurs
        $this->lancementRequetes($requetes, "DELETE");
    }
    
    /*Fonction pour aller chercher tous les champs d'une table et renvoyer un tableau
     * avec tous ces champs
     * $nomTable : une seule table possible si tableau avec plusieurs tables, seule 
     *             la première est prise en compte
     * Retourne un tableau avec la liste des champs
     */
    public function listeChampsTable($nomTable) {
        //ici création directe de la requete qui est simple
        $nomTable = (array) $nomTable;
        $table = $nomTable[0];
        
        $requetes = "SHOW COLUMNS FROM $table";
        $tabChamps = $this->lancementRequetes($requetes, "SHOW_COLUMNS");
        $tabNomChamps = array();
        foreach ($tabChamps as $values){
            $tabNomChamps[]= $values['Field'];
        }
        return $tabNomChamps;
    }
}
