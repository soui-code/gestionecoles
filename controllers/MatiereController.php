<?php
// controllers/MatiereController.php

require_once 'models/matiere.php';
require_once 'models/classe.php';
require_once 'middleware/auth.php';

class MatiereController {
    private $matiereModel;
    private $classeModel;

    public function __construct() {
        Auth::role('super_admin', 'admin', 'enseignant');
        $this->matiereModel = new Matiere();
        $this->classeModel  = new Classe();
    }

    // ─────────────────────────────────────────
    // LISTE DES MATIÈRES
    // GET /gestionecole/matiere/index
    // ─────────────────────────────────────────
    public function index() {
        $currentUser = Auth::user();

        if ($currentUser['role'] === 'super_admin') {
            $matieres = $this->matiereModel->findAll();
        } else {
            $matieres = $this->matiereModel->findByEcole($currentUser['ecole_id']);
        }

        // Grouper par classe
        $matieresByClasse = [];
        foreach ($matieres as $matiere) {
            $matieresByClasse[$matiere['classe_nom']][] = $matiere;
        }

        require_once 'views/matieres/index.php';
    }

    // ─────────────────────────────────────────
    // LISTE DES MATIÈRES D'UNE CLASSE
    // GET /gestionecole/matiere/classe?classe_id=1
    // ─────────────────────────────────────────
    public function classe() {
        $currentUser = Auth::user();
        $classe_id   = $_GET['classe_id'] ?? null;

        if (!$classe_id) {
            header('Location: /gestionecole/matiere/index');
            exit;
        }

        $classe   = $this->classeModel->findById($classe_id);
        $matieres = $this->matiereModel->findByClasse($classe_id);

        if (!$classe) {
            $_SESSION['error'] = 'Classe introuvable.';
            header('Location: /gestionecole/matiere/index');
            exit;
        }

        require_once 'views/matieres/classe.php';
    }

    // ─────────────────────────────────────────
    // FORMULAIRE CRÉATION
    // GET  /gestionecole/matiere/create?classe_id=1
    // POST /gestionecole/matiere/create
    // ─────────────────────────────────────────
    public function create() {
        Auth::role('super_admin', 'admin');
        $currentUser = Auth::user();
        $classe_id   = $_GET['classe_id'] ?? $_POST['classe_id'] ?? null;

        // Charger les classes de l'école
        $classes = $this->classeModel->findByEcole($currentUser['ecole_id']);

        // Classe sélectionnée
        $classe = $classe_id ? $this->classeModel->findById($classe_id) : null;

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            require_once 'views/matieres/create.php';
            return;
        }

        $classe_id   = $_POST['classe_id'] ?? null;
        $nom         = trim($_POST['nom'] ?? '');
        $note_sur    = (float)($_POST['note_sur'] ?? 10);
        $ecole_id    = $currentUser['ecole_id'];

        // 1. Validation
        if (empty($classe_id)) {
            $_SESSION['error'] = 'Veuillez choisir une classe.';
            header('Location: /gestionecole/matiere/create');
            exit;
        }

        if (empty($nom)) {
            $_SESSION['error'] = 'Le nom de la matière est obligatoire.';
            header('Location: /gestionecole/matiere/create?classe_id=' . $classe_id);
            exit;
        }


        if (!in_array($note_sur, [5, 10, 20, 50, 100])) {
            $_SESSION['error'] = 'La note doit être sur 5, 10, 20, 50 ou 100.';
            header('Location: /gestionecole/matiere/create?classe_id=' . $classe_id);
            exit;
        }

        // 2. Vérifier doublon dans la même classe
        if ($this->matiereModel->matiereExists($classe_id, $nom)) {
            $_SESSION['error'] = 'Cette matière existe déjà dans cette classe.';
            header('Location: /gestionecole/matiere/create?classe_id=' . $classe_id);
            exit;
        }

        // 3. Créer la matière
        $this->matiereModel->create($ecole_id, $classe_id, $nom, $note_sur);

        $_SESSION['success'] = 'Matière créée avec succès.';
        header('Location: /gestionecole/matiere/classe?classe_id=' . $classe_id);
        exit;
    }

    // ─────────────────────────────────────────
    // FORMULAIRE MODIFICATION
    // GET  /gestionecole/matiere/edit?id=1
    // POST /gestionecole/matiere/edit?id=1
    // ─────────────────────────────────────────
    public function edit() {
        Auth::role('super_admin', 'admin');
        $currentUser = Auth::user();
        $id = $_GET['id'] ?? null;

        if (!$id) {
            header('Location: /gestionecole/matiere/index');
            exit;
        }

        $matiere = $this->matiereModel->findById($id);

        if (!$matiere) {
            $_SESSION['error'] = 'Matière introuvable.';
            header('Location: /gestionecole/matiere/index');
            exit;
        }

        // Admin ne peut pas modifier une matière d'une autre école
        if ($currentUser['role'] === 'admin' &&
            $matiere['ecole_id'] != $currentUser['ecole_id']) {
            $_SESSION['error'] = 'Action non autorisée.';
            header('Location: /gestionecole/matiere/index');
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            require_once 'views/matieres/edit.php';
            return;
        }

        $nom         = trim($_POST['nom'] ?? '');
        $coefficient = (int)($_POST['coefficient'] ?? 1);
        $note_sur    = (float)($_POST['note_sur'] ?? 10);

        // 1. Validation
        if (empty($nom)) {
            $_SESSION['error'] = 'Le nom de la matière est obligatoire.';
            header('Location: /gestionecole/matiere/edit?id=' . $id);
            exit;
        }

        if ($coefficient < 1 || $coefficient > 10) {
            $_SESSION['error'] = 'Le coefficient doit être entre 1 et 10.';
            header('Location: /gestionecole/matiere/edit?id=' . $id);
            exit;
        }

        if (!in_array($note_sur, [5, 10, 20, 50, 100])) {
            $_SESSION['error'] = 'La note doit être sur 5, 10, 20, 50 ou 100.';
            header('Location: /gestionecole/matiere/edit?id=' . $id);
            exit;
        }

        // 2. Vérifier doublon
        if ($this->matiereModel->matiereExists($matiere['classe_id'], $nom, $id)) {
            $_SESSION['error'] = 'Cette matière existe déjà dans cette classe.';
            header('Location: /gestionecole/matiere/edit?id=' . $id);
            exit;
        }

        // 3. Modifier
        $this->matiereModel->update($id, $nom, $coefficient, $note_sur);

        $_SESSION['success'] = 'Matière modifiée avec succès.';
        header('Location: /gestionecole/matiere/classe?classe_id=' . $matiere['classe_id']);
        exit;
    }

    // ─────────────────────────────────────────
    // SUPPRESSION
    // GET /gestionecole/matiere/delete?id=1
    // ─────────────────────────────────────────
    public function delete() {
        Auth::role('super_admin', 'admin');
        $currentUser = Auth::user();
        $id = $_GET['id'] ?? null;

        if (!$id) {
            header('Location: /gestionecole/matiere/index');
            exit;
        }

        $matiere = $this->matiereModel->findById($id);

        if (!$matiere) {
            $_SESSION['error'] = 'Matière introuvable.';
            header('Location: /gestionecole/matiere/index');
            exit;
        }

        if ($currentUser['role'] === 'admin' &&
            $matiere['ecole_id'] != $currentUser['ecole_id']) {
            $_SESSION['error'] = 'Action non autorisée.';
            header('Location: /gestionecole/matiere/index');
            exit;
        }

        $classe_id = $matiere['classe_id'];
        $this->matiereModel->delete($id);

        $_SESSION['success'] = 'Matière supprimée avec succès.';
        header('Location: /gestionecole/matiere/classe?classe_id=' . $classe_id);
        exit;
    }
}