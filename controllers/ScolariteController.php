<?php
// controllers/ScolariteController.php

require_once 'models/Scolarite.php';
require_once 'models/Classe.php';
require_once 'models/Annee.php';
require_once 'middleware/auth.php';

class ScolariteController {
    private $scolariteModel;
    private $classeModel;
    private $anneeModel;

    public function __construct() {
        Auth::role('super_admin', 'admin');
        $this->scolariteModel = new Scolarite();
        $this->classeModel    = new Classe();
        $this->anneeModel     = new Annee();
    }

    // ─────────────────────────────────────────
    // LISTE DES SCOLARITÉS
    // GET /gestionecole/scolarite/index
    // ─────────────────────────────────────────
    public function index() {
        $currentUser = Auth::user();
        $anneeActive = $this->anneeModel->findActive();

        if (!$anneeActive) {
            $_SESSION['error'] = 'Aucune année scolaire active.';
            header('Location: /gestionecole/annee/index');
            exit;
        }

        $scolarites = $this->scolariteModel->findByAnnee(
            $currentUser['ecole_id'],
            $anneeActive['id']
        );

        // Classes sans scolarité définie
        $classes        = $this->classeModel->findByEcole($currentUser['ecole_id']);
        $classesSansTarif = [];

        foreach ($classes as $classe) {
            if (!$this->scolariteModel->exists(
                $currentUser['ecole_id'],
                $classe['id'],
                $anneeActive['id']
            )) {
                $classesSansTarif[] = $classe;
            }
        }

        require_once 'views/scolarites/index.php';
    }

    // ─────────────────────────────────────────
    // FORMULAIRE CRÉATION
    // GET  /gestionecole/scolarite/create
    // POST /gestionecole/scolarite/create
    // ─────────────────────────────────────────
    public function create() {
        $currentUser = Auth::user();
        $anneeActive = $this->anneeModel->findActive();

        if (!$anneeActive) {
            $_SESSION['error'] = 'Aucune année scolaire active.';
            header('Location: /gestionecole/annee/index');
            exit;
        }

        // Charger uniquement les classes sans tarif défini
        $classes = [];
        $toutesClasses = $this->classeModel->findByEcole($currentUser['ecole_id']);

        foreach ($toutesClasses as $classe) {
            if (!$this->scolariteModel->exists(
                $currentUser['ecole_id'],
                $classe['id'],
                $anneeActive['id']
            )) {
                $classes[] = $classe;
            }
        }

        if (empty($classes)) {
            $_SESSION['error'] = 'Toutes les classes ont déjà un tarif défini pour cette année.';
            header('Location: /gestionecole/scolarite/index');
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            require_once 'views/scolarites/create.php';
            return;
        }

        $classe_id           = $_POST['classe_id'] ?? null;
        $montant_inscription = (float)($_POST['montant_inscription'] ?? 0);
        $montant_mensuel     = (float)($_POST['montant_mensuel'] ?? 0);
        $montant_annuel      = (float)($_POST['montant_annuel'] ?? 0);

        // 1. Validation
        if (empty($classe_id)) {
            $_SESSION['error'] = 'Veuillez choisir une classe.';
            header('Location: /gestionecole/scolarite/create');
            exit;
        }

        if ($montant_inscription < 0 || $montant_mensuel < 0 || $montant_annuel < 0) {
            $_SESSION['error'] = 'Les montants ne peuvent pas être négatifs.';
            header('Location: /gestionecole/scolarite/create');
            exit;
        }

        if ($montant_annuel <= 0) {
            $_SESSION['error'] = 'Le montant annuel est obligatoire.';
            header('Location: /gestionecole/scolarite/create');
            exit;
        }

        // 2. Vérifier que la classe appartient à l'école
        $classe = $this->classeModel->findById($classe_id);
        if (!$classe || $classe['ecole_id'] != $currentUser['ecole_id']) {
            $_SESSION['error'] = 'Classe invalide.';
            header('Location: /gestionecole/scolarite/create');
            exit;
        }

        // 3. Enregistrer
        $this->scolariteModel->save(
            $currentUser['ecole_id'],
            $classe_id,
            $anneeActive['id'],
            $montant_inscription,
            $montant_mensuel,
            $montant_annuel
        );

        $_SESSION['success'] = 'Tarif de scolarité enregistré avec succès.';
        header('Location: /gestionecole/scolarite/index');
        exit;
    }

    // ─────────────────────────────────────────
    // FORMULAIRE MODIFICATION
    // GET  /gestionecole/scolarite/edit?id=1
    // POST /gestionecole/scolarite/edit?id=1
    // ─────────────────────────────────────────
    public function edit() {
        $currentUser = Auth::user();
        $id = $_GET['id'] ?? null;

        if (!$id) {
            header('Location: /gestionecole/scolarite/index');
            exit;
        }

        $scolarite = $this->scolariteModel->findById($id);

        if (!$scolarite) {
            $_SESSION['error'] = 'Scolarité introuvable.';
            header('Location: /gestionecole/scolarite/index');
            exit;
        }

        // Admin ne peut pas modifier une scolarité d'une autre école
        if ($currentUser['role'] === 'admin' &&
            $scolarite['ecole_id'] != $currentUser['ecole_id']) {
            $_SESSION['error'] = 'Action non autorisée.';
            header('Location: /gestionecole/scolarite/index');
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            require_once 'views/scolarites/edit.php';
            return;
        }

        $montant_inscription = (float)($_POST['montant_inscription'] ?? 0);
        $montant_mensuel     = (float)($_POST['montant_mensuel'] ?? 0);
        $montant_annuel      = (float)($_POST['montant_annuel'] ?? 0);

        // 1. Validation
        if ($montant_inscription < 0 || $montant_mensuel < 0 || $montant_annuel < 0) {
            $_SESSION['error'] = 'Les montants ne peuvent pas être négatifs.';
            header('Location: /gestionecole/scolarite/edit?id=' . $id);
            exit;
        }

        if ($montant_annuel <= 0) {
            $_SESSION['error'] = 'Le montant annuel est obligatoire.';
            header('Location: /gestionecole/scolarite/edit?id=' . $id);
            exit;
        }

        // 2. Mettre à jour
        $this->scolariteModel->save(
            $scolarite['ecole_id'],
            $scolarite['classe_id'],
            $scolarite['annee_id'],
            $montant_inscription,
            $montant_mensuel,
            $montant_annuel
        );

        $_SESSION['success'] = 'Tarif de scolarité modifié avec succès.';
        header('Location: /gestionecole/scolarite/index');
        exit;
    }

    // ─────────────────────────────────────────
    // SUPPRESSION
    // GET /gestionecole/scolarite/delete?id=1
    // ─────────────────────────────────────────
    public function delete() {
        $currentUser = Auth::user();
        $id = $_GET['id'] ?? null;

        if (!$id) {
            header('Location: /gestionecole/scolarite/index');
            exit;
        }

        $scolarite = $this->scolariteModel->findById($id);

        if (!$scolarite) {
            $_SESSION['error'] = 'Scolarité introuvable.';
            header('Location: /gestionecole/scolarite/index');
            exit;
        }

        // Admin ne peut pas supprimer une scolarité d'une autre école
        if ($currentUser['role'] === 'admin' &&
            $scolarite['ecole_id'] != $currentUser['ecole_id']) {
            $_SESSION['error'] = 'Action non autorisée.';
            header('Location: /gestionecole/scolarite/index');
            exit;
        }

        $this->scolariteModel->delete($id, $currentUser['ecole_id']);

        $_SESSION['success'] = 'Tarif supprimé avec succès.';
        header('Location: /gestionecole/scolarite/index');
        exit;
    }

    // ─────────────────────────────────────────
    // RÉSUMÉ FINANCIER PAR CLASSE
    // GET /gestionecole/scolarite/resume
    // ─────────────────────────────────────────
    public function resume() {
        $currentUser = Auth::user();
        $anneeActive = $this->anneeModel->findActive();

        if (!$anneeActive) {
            $_SESSION['error'] = 'Aucune année scolaire active.';
            header('Location: /gestionecole/annee/index');
            exit;
        }

        $resume = $this->scolariteModel->getResumeFinancierByClasse(
            $currentUser['ecole_id'],
            $anneeActive['id']
        );

        require_once 'views/scolarites/resume.php';
    }

    // ─────────────────────────────────────────
    // ÉLÈVES AVEC STATUT PAIEMENT PAR CLASSE
    // GET /gestionecole/scolarite/eleves?classe_id=1
    // ─────────────────────────────────────────
    public function eleves() {
        Auth::role('super_admin', 'admin', 'secretaire');
        $currentUser = Auth::user();
        $anneeActive = $this->anneeModel->findActive();
        $classe_id   = $_GET['classe_id'] ?? null;

        if (!$classe_id) {
            header('Location: /gestionecole/scolarite/index');
            exit;
        }

        if (!$anneeActive) {
            $_SESSION['error'] = 'Aucune année scolaire active.';
            header('Location: /gestionecole/annee/index');
            exit;
        }

        $classe = $this->classeModel->findById($classe_id);

        if (!$classe || $classe['ecole_id'] != $currentUser['ecole_id']) {
            $_SESSION['error'] = 'Classe invalide.';
            header('Location: /gestionecole/scolarite/index');
            exit;
        }

        $eleves = $this->scolariteModel->getElevesAvecStatutPaiement(
            $currentUser['ecole_id'],
            $classe_id,
            $anneeActive['id']
        );

        require_once 'views/scolarites/eleves.php';
    }
}