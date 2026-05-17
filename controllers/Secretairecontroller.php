<?php
// controllers/SecretaireController.php

require_once 'models/Secretaire.php';
require_once 'models/Inscription.php';
require_once 'models/Paiement.php';
require_once 'models/Eleve.php';
require_once 'models/Classe.php';
require_once 'models/Annee.php';
require_once 'config/database.php';
require_once 'middleware/auth.php';

class SecretaireController {
    private $db;
    private $secretaireModel;
    private $inscriptionModel;
    private $paiementModel;
    private $eleveModel;
    private $classeModel;
    private $anneeModel;

    public function __construct() {
        Auth::role('secretaire', 'admin', 'super_admin');
        $database               = new Database();
        $this->db               = $database->getConnection();
        $this->secretaireModel  = new Secretaire();
        $this->inscriptionModel = new Inscription();
        $this->paiementModel    = new Paiement();
        $this->eleveModel       = new Eleve();
        $this->classeModel      = new Classe();
        $this->anneeModel       = new Annee();
    }

    // ─────────────────────────────────────────
    // DASHBOARD
    // GET /gestionecole/secretaire/dashboard
    // ─────────────────────────────────────────
    public function dashboard() {
        $currentUser = Auth::user();
        $anneeActive = $this->anneeModel->findActive();

        if (!$anneeActive) {
            $_SESSION['error'] = 'Aucune année scolaire active.';
            header('Location: /gestionecole/annee/index');
            exit;
        }

        $ecole_id = $currentUser['ecole_id'];
        $annee_id = $anneeActive['id'];

        // Stats générales
        $stats = $this->secretaireModel->getDashboardStats($ecole_id, $annee_id);

        // Résumé par classe
        $resumeClasses = $this->secretaireModel->getResumeParClasse($ecole_id, $annee_id);

        // Derniers paiements (5 derniers)
        $derniersPaiements = $this->secretaireModel->getPaiementsParAnnee($ecole_id, $annee_id);
        $derniersPaiements = array_slice($derniersPaiements, 0, 5);

        // Liste des classes
        $classes = $this->classeModel->findByEcole($ecole_id);

        require_once 'views/secretaire/dashboard.php';
    }

    // ─────────────────────────────────────────
    // LISTE DES PAIEMENTS
    // GET /gestionecole/secretaire/paiements
    // ─────────────────────────────────────────
    public function paiements() {
        $currentUser = Auth::user();
        $anneeActive = $this->anneeModel->findActive();

        if (!$anneeActive) {
            $_SESSION['error'] = 'Aucune année scolaire active.';
            header('Location: /gestionecole/annee/index');
            exit;
        }

        $ecole_id = $currentUser['ecole_id'];
        $annee_id = $anneeActive['id'];
        $search   = trim($_GET['search'] ?? '');

        $paiements = $this->secretaireModel->getPaiementsParAnnee($ecole_id, $annee_id, $search);
        $classes   = $this->classeModel->findByEcole($ecole_id);

        require_once 'views/secretaire/paiements.php';
    }

    // ─────────────────────────────────────────
    // NOUVEAU PAIEMENT COMPLÉMENTAIRE
    // GET  /gestionecole/secretaire/ajouterPaiement?inscription_id=1
    // POST /gestionecole/secretaire/ajouterPaiement
    // ─────────────────────────────────────────
    public function ajouterPaiement() {
        $currentUser = Auth::user();
        $anneeActive = $this->anneeModel->findActive();

        if (!$anneeActive) {
            $_SESSION['error'] = 'Aucune année scolaire active.';
            header('Location: /gestionecole/annee/index');
            exit;
        }

        $inscription_id = $_GET['inscription_id'] ?? $_POST['inscription_id'] ?? null;

        if (!$inscription_id) {
            header('Location: /gestionecole/secretaire/soldes');
            exit;
        }

        $fiche = $this->secretaireModel->getFicheSolde($inscription_id, $currentUser['ecole_id']);

        if (!$fiche) {
            $_SESSION['error'] = 'Inscription introuvable.';
            header('Location: /gestionecole/secretaire/soldes');
            exit;
        }

        // Vérifier que l'inscription appartient bien à l'école
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $historique = $this->secretaireModel->getHistoriquePaiementsEleve($inscription_id);
            require_once 'views/secretaire/ajouter_paiement.php';
            return;
        }

        // — Traitement POST —
        $montant       = (float)($_POST['montant'] ?? 0);
        $date_paiement = trim($_POST['date_paiement'] ?? date('Y-m-d H:i:s'));
        $statut        = trim($_POST['statut'] ?? 'partiel');

        // 1. Validation
        if ($montant <= 0) {
            $_SESSION['error'] = 'Le montant doit être supérieur à 0.';
            header('Location: /gestionecole/secretaire/ajouterPaiement?inscription_id=' . $inscription_id);
            exit;
        }

        if (!in_array($statut, ['paye', 'partiel', 'impaye'])) {
            $_SESSION['error'] = 'Statut invalide.';
            header('Location: /gestionecole/secretaire/ajouterPaiement?inscription_id=' . $inscription_id);
            exit;
        }

        // 2. Transaction
        $this->db->beginTransaction();
        try {
            $paiement_id = $this->secretaireModel->ajouterPaiement(
                $currentUser['ecole_id'],
                $inscription_id,
                $montant,
                $date_paiement,
                $statut,
                $anneeActive['id']
            );
            $this->db->commit();

            $_SESSION['success'] = 'Paiement enregistré avec succès.';
            header('Location: /gestionecole/secretaire/recu?paiement_id=' . $paiement_id);
            exit;

        } catch (Exception $e) {
            $this->db->rollBack();
            $_SESSION['error'] = 'Erreur : ' . $e->getMessage();
            header('Location: /gestionecole/secretaire/ajouterPaiement?inscription_id=' . $inscription_id);
            exit;
        }
    }

    // ─────────────────────────────────────────
    // REÇU / IMPRESSION
    // GET /gestionecole/secretaire/recu?paiement_id=1
    // ─────────────────────────────────────────
    public function recu() {
        $currentUser = Auth::user();
        $paiement_id = $_GET['paiement_id'] ?? null;

        if (!$paiement_id) {
            header('Location: /gestionecole/secretaire/paiements');
            exit;
        }

        $recu = $this->secretaireModel->getRecuComplet($paiement_id, $currentUser['ecole_id']);

        if (!$recu) {
            $_SESSION['error'] = 'Reçu introuvable.';
            header('Location: /gestionecole/secretaire/paiements');
            exit;
        }

        // Historique des paiements de cet élève pour ce reçu
        $historique = $this->secretaireModel->getHistoriquePaiementsEleve($recu['inscription_id']);

        require_once 'views/secretaire/recu.php';
    }

    // ─────────────────────────────────────────
    // SUIVI DES SOLDES
    // GET /gestionecole/secretaire/soldes
    // ─────────────────────────────────────────
    public function soldes() {
        $currentUser = Auth::user();
        $anneeActive = $this->anneeModel->findActive();

        if (!$anneeActive) {
            $_SESSION['error'] = 'Aucune année scolaire active.';
            header('Location: /gestionecole/annee/index');
            exit;
        }

        $ecole_id  = $currentUser['ecole_id'];
        $annee_id  = $anneeActive['id'];
        $classe_id = $_GET['classe_id'] ?? null;
        $statut    = $_GET['statut'] ?? null;
        $search    = trim($_GET['search'] ?? '');

        $eleves  = $this->secretaireModel->getSuiviSoldesParClasse(
            $ecole_id, $annee_id, $classe_id, $statut, $search
        );
        $classes = $this->classeModel->findByEcole($ecole_id);
        $resume  = $this->secretaireModel->getResumeParClasse($ecole_id, $annee_id);

        require_once 'views/secretaire/soldes.php';
    }

    // ─────────────────────────────────────────
    // FICHE ÉLÈVE (solde détaillé)
    // GET /gestionecole/secretaire/ficheEleve?inscription_id=1
    // ─────────────────────────────────────────
    public function ficheEleve() {
        $currentUser    = Auth::user();
        $inscription_id = $_GET['inscription_id'] ?? null;

        if (!$inscription_id) {
            header('Location: /gestionecole/secretaire/soldes');
            exit;
        }

        $fiche = $this->secretaireModel->getFicheSolde($inscription_id, $currentUser['ecole_id']);

        if (!$fiche) {
            $_SESSION['error'] = 'Fiche introuvable.';
            header('Location: /gestionecole/secretaire/soldes');
            exit;
        }

        $historique = $this->secretaireModel->getHistoriquePaiementsEleve($inscription_id);

        require_once 'views/secretaire/fiche_eleve.php';
    }

    // ─────────────────────────────────────────
    // RECHERCHE AJAX D'UN ÉLÈVE
    // GET /gestionecole/secretaire/rechercherEleve?q=dupont
    // ─────────────────────────────────────────
    public function rechercherEleve() {
        $currentUser = Auth::user();
        $anneeActive = $this->anneeModel->findActive();

        header('Content-Type: application/json');

        if (!$anneeActive) {
            echo json_encode([]);
            exit;
        }

        $query = trim($_GET['q'] ?? '');

        if (strlen($query) < 2) {
            echo json_encode([]);
            exit;
        }

        $resultats = $this->secretaireModel->rechercherEleve(
            $currentUser['ecole_id'],
            $anneeActive['id'],
            $query
        );

        echo json_encode($resultats);
        exit;
    }
}