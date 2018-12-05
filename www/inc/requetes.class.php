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
    public $nomTable;
    public $nomChamps = array('*');
    public $valeurChamps = array("");
    public $champsCondition = false;
    public $valeurCondition = '';
    public $champsClassement = false;

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
        echo "<BR>" . $requetes . "<BR>";
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
        }
    }

    /* --------------------LANCEMENT D'UNE REQUETE SELECTION----------------
      fonction qui lance une requete standard de selection avec les possibilités de
      conditions de classement, ...
      Variables d'entrée voir les noms explicites avec possibilité d'avoir une variable
      ou plusieurs (sous forme de tableau). Attention condition simple champ=valeur
      Retourne un tableau avec toutes les valeurs */
    function RequeteSelection() {
        //inserer des champs specifiques a selectionner
        $this->nomChamps = (array) $this->nomChamps;
        $Champs = $this->nomChamps[0];
        for ($i = 1; $i < count($this->nomChamps); $i++) {
            $Champs = $Champs . ", " . $this->nomChamps[$i];
        }
        $requetes = "SELECT " . $Champs;

        //inserer la ou les tables a selectionner
        $this->nomTable = (array) $this->nomTable;
        $table = $this->nomTable[0];
        for ($i = 1; $i < count($this->nomTable); $i++) {
            $table = $table . " NATURAL JOIN " . $this->nomTable[$i];
        }
        $requetes = $requetes . " FROM " . $table;

        //inserer les conditions de selection
        if ($this->champsCondition) {
            $this->champsCondition = (array) $this->champsCondition;
            $this->valeurCondition = (array) $this->valeurCondition;
            $Condition = $this->champsCondition[0] . "= \"" . $this->valeurCondition[0] . "\"";
            for ($i = 1; $i < count($this->champsCondition); $i++) {
                $Condition = $Condition . " AND " . $this->champsCondition[$i] . "= \"" . $this->valeurCondition[$i] . "\"";
            }
            $requetes = $requetes . " WHERE " . $Condition;
        }

        //inserer le champs de classement
        if ($this->champsClassement) {
            $this->champsClassement = (array) $this->champsClassement;
            $Classement = $this->champsClassement[0];
            for ($i = 1; $i < count($this->champsClassement); $i++) {
                $Classement = $Classement . ", " . $this->champsClassement[$i];
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
     */
    function RequeteModification() {
        //inserer la table a selectionner
        $table = $this->nomTable;
        $requetes = "UPDATE " . $table;

        //inserer les champs a modifier et leur valeur
        $this->nomChamps = (array) $this->nomChamps;
        $this->valeurChamps = (array) $this->valeurChamps;
        $Champs = $this->nomChamps[0] . " = \"" . $this->valeurChamps[0] . "\"";
        for ($i = 1; $i < count($this->nomChamps); $i++) {
            $Champs = $Champs . ", " . $this->nomChamps[$i] . " = \"" . $this->valeurChamps[$i] . "\"";
        }
        $requetes = $requetes . " SET " . $Champs;

        //inserer les conditions de selection
        $this->champsCondition = (array) $this->champsCondition;
        $this->valeurCondition = (array) $this->valeurCondition;
        $Condition = $this->champsCondition[0] . "=\"" . $this->valeurCondition[0] . "\"";
        for ($i = 1; $i < count($this->champsCondition); $i++) {
            $Condition = $Condition . " AND " . $this->champsCondition[$i] . "=\"" . $this->valeurCondition[$i] . "\"";
        }
        $requetes = $requetes . " WHERE " . $Condition;

        //envoyer la requetes
        //lancement de la fonction de connexion et query qui nous retourne un tableau avec les valeurs
        $this->lancementRequetes($requetes, "UPDATE");
    }

    /* --------------------LANCEMENT D'UNE REQUETE AJOUT----------------
     * fonction qui lance L'ajout d'un ou plusieurs enregistrements
     */
    public function RequeteAjout() {
        //récupéraiton d'un tableau avec les colonne de la table

        if (count($this->nomTable) > 1){
            echo 'la variable nomTable doit être unique';
            return;
        }
        //Ici une seule table possible donc la variable $table est simple
        $table = $this->nomTable[0];
        $requetes = "SHOW COLUMNS FROM $table";
        $this->nomChamps = $this->listeChampsTable();

                
        //inserer la table a selectionner
        $requetes = "INSERT INTO " . $table;

        //inserer les champs de la table
        $Champs = $this->nomChamps[1];
        for ($i = 2; $i < count($this->nomChamps); $i++) {
            $Champs = $Champs . ", " . $this->nomChamps[$i];
        }
        $requetes = $requetes . " (" . $Champs . ")";

        //inserer les valeurs des champs
        $this->valeurChamps = (array) $this->valeurChamps;
        $Valeurs = " \"" . $this->valeurChamps[0] . "\"";
        for ($i = 1; $i < count($this->valeurChamps); $i++) {
            $Valeurs = $Valeurs . ", \"" . $this->valeurChamps[$i] . "\"";
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
     */

    function requeteSuppression() {
        //inserer la table a selectionner
        $table = $this->nomTable;
        $requetes = "DELETE FROM " . $table;

        //inserer les conditions de suppression si ils existent
        if ($this->champsCondition){
            $this->champsCondition = (array) $this->champsCondition;
            $this->valeurCondition = (array) $this->valeurCondition;
            $Condition = $this->champsCondition[0] . "=\"" . $this->valeurCondition[0] . "\"";
            for ($i = 1; $i < count($this->champsCondition); $i++) {
                $Condition = $Condition . " AND " . $this->champsCondition[$i] . "=\"" . $this->valeurCondition[$i] . "\"";
            }
            $requetes = $requetes . " WHERE " . $Condition;
        }

        //envoyer la requetes
        //lancement de la fonction de connexion et query qui nous retourne un tableau avec les valeurs
        $this->lancementRequetes($requetes, "DELETE");
    }
    
    /*Fonction pour aller chercher tous les champs d'une table et renvoyer un tableau
     * avec tous ces champs
     */
    public function listeChampsTable() {
        //ici création directe de la requete qui est simple
        $table = $this->nomTable[0];
        $requetes = "SHOW COLUMNS FROM $table";
        $tabChamps = $this->lancementRequetes($requetes, "SHOW_COLUMNS");
        $tabNomChamps = array();
        foreach ($tabChamps as $values){
            $tabNomChamps[]= $values['Field'];
        }
        return $tabNomChamps;
    }
}
