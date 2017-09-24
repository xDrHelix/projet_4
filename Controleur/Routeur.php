<?php

require_once 'Controleur/ControleurAccueil.php';
require_once 'Controleur/ControleurBillet.php';
require_once 'Controleur/ControleurAdmin.php';
require_once 'Vue/Vue.php';

class Routeur {

    private $ctrlAccueil;
    private $ctrlBillet;
    private $ctrlSession;

    public function __construct() {
        $this->ctrlAccueil = new ControleurAccueil();
        $this->ctrlBillet = new ControleurBillet();
        $this->ctrlAdmin = new ControleurAdmin();
    }

    // Route une requête entrante : exécution l'action associée
    public function routerRequete() {
        try {
            if (isset($_GET['action'])) {
                if ($_GET['action'] == 'billet') {
                    $idBillet = intval($this->getParametre($_GET, 'id'));
                    if ($idBillet != 0) {
                        if ($_SESSION['login'] == true) {
                            $this->ctrlAdmin->billetAdmin($idBillet);
                        }
                        else {
                            $this->ctrlBillet->billet($idBillet);
                        }
                    }
                    else
                        throw new Exception("Identifiant de billet non valide");
                }
                else if ($_GET['action'] == 'commenter') {
                    $auteur = $this->getParametre($_POST, 'auteur');
                    $contenu = $this->getParametre($_POST, 'contenu');
                    $idBillet = $this->getParametre($_POST, 'id');
                    $this->ctrlBillet->commenter($auteur, $contenu, $idBillet);
                }
                else if ($_GET['action'] == 'connexion') {
                    $login = $this->getParametre($_POST, 'login');
                    $mdp = $this->getParametre($_POST, 'mdp');
                    $this->ctrlAdmin->connect($login, $mdp);
                }
                else if ($_GET['action'] == 'deconnexion') {
                    $this->ctrlAdmin->deconnecter();
                }
                else if ($_GET['action'] == 'admin') {
                    if ($_SESSION['login'] == 'true') {
                        $this->ctrlAdmin->pageAdmin();
                    }
                    else
                        throw new Exception("Accès non autorisé");
                }
                else if ($_GET['action'] == 'supprimerBillet') {
                    if ($_SESSION['login'] == 'true') {
                        $idBillet = intval($this->getParametre($_GET, 'id'));
                        $this->ctrlBillet->supprimer($idBillet);
                    }
                    else
                        throw new Exception("Accès non autorisé");
                }
                else if ($_GET['action'] == 'modifierBillet') {
                    if ($_SESSION['login'] == 'true') {
                        $idBillet = intval($this->getParametre($_GET, 'id'));
                        $this->ctrlBillet->modifier($idBillet);
                    }
                    else
                        throw new Exception("Accès non autorisé");
                }
                else if ($_GET['action'] == 'nouveauBillet') {
                    $vue = new Vue("FormCreationBillet");
                    $vue->generer(array());
                }
                else if ($_GET['action'] == 'ajouterBillet') {
                    $titre = $this->getParametre($_POST, 'titre');
                    $contenu = $this->getParametre($_POST, 'contenu');
                    $this->ctrlBillet->ajouter($titre, $contenu);
                }
                else
                    throw new Exception("Action non valide");
            }
            else {  // aucune action définie : affichage de l'accueil
                $this->ctrlAccueil->accueil();
            }
        }
        catch (Exception $e) {
            $this->erreur($e->getMessage());
        }
    }

    // Affiche une erreur
    private function erreur($msgErreur) {
        $vue = new Vue("Erreur");
        $vue->generer(array('msgErreur' => $msgErreur));
    }

    // Recherche un paramètre dans un tableau
    private function getParametre($tableau, $nom) {
        if (isset($tableau[$nom])) {
            return $tableau[$nom];
        }
        else
            throw new Exception("Paramètre '$nom' absent");
    }

}
