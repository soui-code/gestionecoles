<?php
// controllers/CompositionController.php

require_once 'models/composition.php';
require_once 'models/annee.php';
require_once 'middleware/auth.php';

class CompositionController {
    private $compositionModel;
    private $anneeModel;

    public function __construct() {
        Auth::role('super_admin', 'admin');
        $this->compositionModel = new Composition();
        $this->anneeModel       = new Annee();
    }

    // ─────────────────────────────────────────
    // LISTE DES COMPOSITIONS
    // GET /gestionecole/composition/index
    // ─────────────────────────────────────────
    public function index() {
        $currentUser = Auth::user();
        $anneeActive = $this->anneeModel->findActive();

        if (!$anneeActive) {
            $_SESSION['error'] = 'Aucune année scolaire active.';
            header('Location: /gestionecole/annee/index');
            exit;
        }

        $compositions = $this->compositionModel->findByAnnee(
            $currentUser['ecole_id'],
            $anneeActive['id']
        );

        $totalCompositions = count($compositions);

        require_once 'views/compositions/index.php';
    }

    // ─────────────────────────────────────────
    // FORMULAIRE CRÉATION
    // GET  /gestionecole/composition/create
    // POST /gestionecole/composition/create
    // ─────────────────────────────────────────
    public function create() {
        $currentUser = Auth::user();
        $anneeActive = $this->anneeModel->findActive();

        if (!$anneeActive) {
            $_SESSION['error'] = 'Aucune année scolaire active.';
            header('Location: /gestionecole/annee/index');
            exit;
        }

        // Prochain ordre suggéré
        $nextOrdre = $this->compositionModel->getNextOrdre(
            $currentUser['ecole_id'],
            $anneeActive['id']
        );

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            require_once 'views/compositions/create.php';
            return;
        }

        $libelle    = trim($_POST['libelle'] ?? '');
        $ordre      = (int)($_POST['ordre'] ?? $nextOrdre);
        $date_debut = trim($_POST['date_debut'] ?? '');
        $date_fin   = trim($_POST['date_fin'] ?? '');

        // 1. Validation
        if (empty($libelle)) {
            $_SESSION['error'] = 'Le libellé est obligatoire.';
            header('Location: /gestionecole/composition/create');
            exit;
        }

        if (empty($date_debut) || empty($date_fin)) {
            $_SESSION['error'] = 'Les dates de début et de fin sont obligatoires.';
            header('Location: /gestionecole/composition/create');
            exit;
        }

        if ($date_debut >= $date_fin) {
            $_SESSION['error'] = 'La date de fin doit être après la date de début.';
            header('Location: /gestionecole/composition/create');
            exit;
        }

        if ($ordre < 1) {
            $_SESSION['error'] = 'L\'ordre doit être supérieur à 0.';
            header('Location: /gestionecole/composition/create');
            exit;
        }

        // 2. Vérifier doublon libellé
        if ($this->compositionModel->libelleExists(
            $currentUser['ecole_id'],
            $anneeActive['id'],
            $libelle
        )) {
            $_SESSION['error'] = 'Ce libellé existe déjà pour cette année.';
            header('Location: /gestionecole/composition/create');
            exit;
        }

        // 3. Vérifier doublon ordre
        if ($this->compositionModel->ordreExists(
            $currentUser['ecole_id'],
            $anneeActive['id'],
            $ordre
        )) {
            $_SESSION['error'] = 'Cet ordre existe déjà pour cette année.';
            header('Location: /gestionecole/composition/create');
            exit;
        }

        // 4. Vérifier chevauchement de dates
        if ($this->compositionModel->datesChevauchent(
            $currentUser['ecole_id'],
            $anneeActive['id'],
            $date_debut,
            $date_fin
        )) {
            $_SESSION['error'] = 'Les dates chevauchent une autre composition.';
            header('Location: /gestionecole/composition/create');
            exit;
        }

        // 5. Créer la composition
        $this->compositionModel->create(
            $currentUser['ecole_id'],
            $anneeActive['id'],
            $libelle,
            $ordre,
            $date_debut,
            $date_fin
        );

        $_SESSION['success'] = 'Composition créée avec succès.';
        header('Location: /gestionecole/composition/index');
        exit;
    }

    // ─────────────────────────────────────────
    // FORMULAIRE MODIFICATION
    // GET  /gestionecole/composition/edit?id=1
    // POST /gestionecole/composition/edit?id=1
    // ─────────────────────────────────────────
    public function edit() {
        $currentUser = Auth::user();
        $id = $_GET['id'] ?? null;

        if (!$id) {
            header('Location: /gestionecole/composition/index');
            exit;
        }

        $composition = $this->compositionModel->findById($id);

        if (!$composition) {
            $_SESSION['error'] = 'Composition introuvable.';
            header('Location: /gestionecole/composition/index');
            exit;
        }

        // Admin ne peut pas modifier une composition d'une autre école
        if ($currentUser['role'] === 'admin' &&
            $composition['ecole_id'] != $currentUser['ecole_id']) {
            $_SESSION['error'] = 'Action non autorisée.';
            header('Location: /gestionecole/composition/index');
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            require_once 'views/compositions/edit.php';
            return;
        }

        $libelle    = trim($_POST['libelle'] ?? '');
        $ordre      = (int)($_POST['ordre'] ?? 1);
        $date_debut = trim($_POST['date_debut'] ?? '');
        $date_fin   = trim($_POST['date_fin'] ?? '');

        // 1. Validation
        if (empty($libelle)) {
            $_SESSION['error'] = 'Le libellé est obligatoire.';
            header('Location: /gestionecole/composition/edit?id=' . $id);
            exit;
        }

        if (empty($date_debut) || empty($date_fin)) {
            $_SESSION['error'] = 'Les dates de début et de fin sont obligatoires.';
            header('Location: /gestionecole/composition/edit?id=' . $id);
            exit;
        }

        if ($date_debut >= $date_fin) {
            $_SESSION['error'] = 'La date de fin doit être après la date de début.';
            header('Location: /gestionecole/composition/edit?id=' . $id);
            exit;
        }

        if ($ordre < 1) {
            $_SESSION['error'] = 'L\'ordre doit être supérieur à 0.';
            header('Location: /gestionecole/composition/edit?id=' . $id);
            exit;
        }

        // 2. Vérifier doublon libellé
        if ($this->compositionModel->libelleExists(
            $composition['ecole_id'],
            $composition['annee_id'],
            $libelle,
            $id
        )) {
            $_SESSION['error'] = 'Ce libellé existe déjà pour cette année.';
            header('Location: /gestionecole/composition/edit?id=' . $id);
            exit;
        }

        // 3. Vérifier doublon ordre
        if ($this->compositionModel->ordreExists(
            $composition['ecole_id'],
            $composition['annee_id'],
            $ordre,
            $id
        )) {
            $_SESSION['error'] = 'Cet ordre existe déjà pour cette année.';
            header('Location: /gestionecole/composition/edit?id=' . $id);
            exit;
        }

        // 4. Vérifier chevauchement de dates
        if ($this->compositionModel->datesChevauchent(
            $composition['ecole_id'],
            $composition['annee_id'],
            $date_debut,
            $date_fin,
            $id
        )) {
            $_SESSION['error'] = 'Les dates chevauchent une autre composition.';
            header('Location: /gestionecole/composition/edit?id=' . $id);
            exit;
        }

        // 5. Modifier la composition
        $this->compositionModel->update($id, $libelle, $ordre, $date_debut, $date_fin);

        $_SESSION['success'] = 'Composition modifiée avec succès.';
        header('Location: /gestionecole/composition/index');
        exit;
    }

    // ─────────────────────────────────────────
    // SUPPRESSION
    // GET /gestionecole/composition/delete?id=1
    // ─────────────────────────────────────────
    public function delete() {
        $currentUser = Auth::user();
        $id = $_GET['id'] ?? null;

        if (!$id) {
            header('Location: /gestionecole/composition/index');
            exit;
        }

        $composition = $this->compositionModel->findById($id);

        if (!$composition) {
            $_SESSION['error'] = 'Composition introuvable.';
            header('Location: /gestionecole/composition/index');
            exit;
        }

        // Admin ne peut pas supprimer une composition d'une autre école
        if ($currentUser['role'] === 'admin' &&
            $composition['ecole_id'] != $currentUser['ecole_id']) {
            $_SESSION['error'] = 'Action non autorisée.';
            header('Location: /gestionecole/composition/index');
            exit;
        }

        // Vérifier minimum 3 compositions
        $total = $this->compositionModel->countByAnnee(
            $composition['ecole_id'],
            $composition['annee_id']
        );

        if ($total <= 3) {
            $_SESSION['error'] = 'Impossible de supprimer — le minimum est 3 compositions par année.';
            header('Location: /gestionecole/composition/index');
            exit;
        }

        $this->compositionModel->delete($id);

        $_SESSION['success'] = 'Composition supprimée avec succès.';
        header('Location: /gestionecole/composition/index');
        exit;
    }
}