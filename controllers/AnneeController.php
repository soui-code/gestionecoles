<?php
// controllers/AnneeController.php

require_once 'models/Annee.php';
require_once 'middleware/auth.php';

class AnneeController {
    private $anneeModel;

    public function __construct() {
        Auth::role('super_admin', 'admin');
        $this->anneeModel = new Annee();
    }

    // ─────────────────────────────────────────
    // LISTE DES ANNÉES
    // GET /gestionecole/annee/index
    // ─────────────────────────────────────────
    public function index() {
        $annees      = $this->anneeModel->findAll();
        $anneeActive = $this->anneeModel->findActive();
        require_once 'views/annees/index.php';
    }

    // ─────────────────────────────────────────
    // FORMULAIRE CRÉATION
    // GET /gestionecole/annee/create
    // ─────────────────────────────────────────
    public function create() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            require_once 'views/annees/create.php';
            return;
        }

        $libelle = trim($_POST['libelle'] ?? '');

        // 1. Validation
        if (empty($libelle)) {
            $_SESSION['error'] = 'Le libellé est obligatoire.';
            header('Location: /gestionecole/annee/create');
            exit;
        }

        // Format : 2024-2025
        if (!preg_match('/^\d{4}-\d{4}$/', $libelle)) {
            $_SESSION['error'] = 'Format invalide. Exemple : 2024-2025';
            header('Location: /gestionecole/annee/create');
            exit;
        }

        // Vérifier cohérence des années
        $parts = explode('-', $libelle);
        if ((int)$parts[1] !== (int)$parts[0] + 1) {
            $_SESSION['error'] = 'Les années doivent être consécutives. Exemple : 2024-2025';
            header('Location: /gestionecole/annee/create');
            exit;
        }

        // 2. Vérifier doublon
        if ($this->anneeModel->libelleExists($libelle)) {
            $_SESSION['error'] = 'Cette année scolaire existe déjà.';
            header('Location: /gestionecole/annee/create');
            exit;
        }

        // 3. Créer l'année
        $this->anneeModel->create($libelle);

        $_SESSION['success'] = 'Année scolaire créée avec succès.';
        header('Location: /gestionecole/annee/index');
        exit;
    }

    // ─────────────────────────────────────────
    // FORMULAIRE MODIFICATION
    // GET /gestionecole/annee/edit?id=1
    // ─────────────────────────────────────────
    public function edit() {
        $id = $_GET['id'] ?? null;

        if (!$id) {
            header('Location: /gestionecole/annee/index');
            exit;
        }

        $annee = $this->anneeModel->findById($id);

        if (!$annee) {
            $_SESSION['error'] = 'Année introuvable.';
            header('Location: /gestionecole/annee/index');
            exit;
        }

        // Ne pas modifier une année active
        if ($annee['etat'] == 1) {
            $_SESSION['error'] = 'Impossible de modifier l\'année scolaire active.';
            header('Location: /gestionecole/annee/index');
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            require_once 'views/annees/edit.php';
            return;
        }

        $libelle = trim($_POST['libelle'] ?? '');

        // 1. Validation
        if (empty($libelle)) {
            $_SESSION['error'] = 'Le libellé est obligatoire.';
            header('Location: /gestionecole/annee/edit?id=' . $id);
            exit;
        }

        // Format : 2024-2025
        if (!preg_match('/^\d{4}-\d{4}$/', $libelle)) {
            $_SESSION['error'] = 'Format invalide. Exemple : 2024-2025';
            header('Location: /gestionecole/annee/edit?id=' . $id);
            exit;
        }

        // Vérifier cohérence des années
        $parts = explode('-', $libelle);
        if ((int)$parts[1] !== (int)$parts[0] + 1) {
            $_SESSION['error'] = 'Les années doivent être consécutives. Exemple : 2024-2025';
            header('Location: /gestionecole/annee/edit?id=' . $id);
            exit;
        }

        // 2. Vérifier doublon
        if ($this->anneeModel->libelleExists($libelle, $id)) {
            $_SESSION['error'] = 'Cette année scolaire existe déjà.';
            header('Location: /gestionecole/annee/edit?id=' . $id);
            exit;
        }

        // 3. Modifier l'année
        $this->anneeModel->update($id, $libelle);

        $_SESSION['success'] = 'Année scolaire modifiée avec succès.';
        header('Location: /gestionecole/annee/index');
        exit;
    }

    // ─────────────────────────────────────────
    // ACTIVER UNE ANNÉE
    // GET /gestionecole/annee/activate?id=1
    // ─────────────────────────────────────────
    public function activate() {
        $id = $_GET['id'] ?? null;

        if (!$id) {
            header('Location: /gestionecole/annee/index');
            exit;
        }

        $annee = $this->anneeModel->findById($id);

        if (!$annee) {
            $_SESSION['error'] = 'Année introuvable.';
            header('Location: /gestionecole/annee/index');
            exit;
        }

        // Déjà active
        if ($annee['etat'] == 1) {
            $_SESSION['error'] = 'Cette année est déjà active.';
            header('Location: /gestionecole/annee/index');
            exit;
        }

        $this->anneeModel->activate($id);

        $_SESSION['success'] = 'Année scolaire ' . $annee['libelle'] . ' activée avec succès.';
        header('Location: /gestionecole/annee/index');
        exit;
    }

    // ─────────────────────────────────────────
    // SUPPRIMER UNE ANNÉE
    // GET /gestionecole/annee/delete?id=1
    // ─────────────────────────────────────────
    public function delete() {
        $id = $_GET['id'] ?? null;

        if (!$id) {
            header('Location: /gestionecole/annee/index');
            exit;
        }

        $annee = $this->anneeModel->findById($id);

        if (!$annee) {
            $_SESSION['error'] = 'Année introuvable.';
            header('Location: /gestionecole/annee/index');
            exit;
        }

        // Ne pas supprimer une année active
        if ($annee['etat'] == 1) {
            $_SESSION['error'] = 'Impossible de supprimer l\'année scolaire active.';
            header('Location: /gestionecole/annee/index');
            exit;
        }

        $this->anneeModel->delete($id);

        $_SESSION['success'] = 'Année scolaire supprimée avec succès.';
        header('Location: /gestionecole/annee/index');
        exit;
    }
}