<?php
// controllers/ClasseController.php

require_once 'models/classe.php';
require_once 'models/enseignant.php';
require_once 'middleware/auth.php';

class ClasseController {
    private $classeModel;
    private $enseignantModel;

    public function __construct() {
        Auth::role('super_admin', 'admin', 'enseignant', 'secretaire');
        $this->classeModel     = new Classe();
        $this->enseignantModel = new Enseignant();
    }

    // ─────────────────────────────────────────
    // LISTE DES CLASSES
    // GET /gestionecole/classe/index
    // ─────────────────────────────────────────
    public function index() {
        $currentUser = Auth::user();

        if ($currentUser['role'] === 'super_admin') {
            $classes = $this->classeModel->findAll();
        } elseif ($currentUser['role'] === 'enseignant') {
            $classes = $this->classeModel->findByEnseignant($currentUser['id']);
        } else {
            // admin, secretaire
            $classes = $this->classeModel->findByEcole($currentUser['ecole_id']);
        }

        require_once 'views/classes/index.php';
    }

    // ─────────────────────────────────────────
    // FORMULAIRE CRÉATION
    // GET /gestionecole/classe/create
    // ─────────────────────────────────────────
    public function create() {
        Auth::role('super_admin', 'admin');
        $currentUser = Auth::user();

        $enseignants = [];
        $ecoles      = [];

        if ($currentUser['role'] === 'super_admin') {
            require_once 'models/ecole.php';
            $ecoleModel = new Ecole();
            $ecoles     = $ecoleModel->findAll();

            // Charger enseignants selon école choisie
            $ecole_id_select = $_GET['ecole_id'] ?? null;
            if ($ecole_id_select) {
                $enseignants = $this->enseignantModel->findEnseignantsByEcole($ecole_id_select);
            }
        } else {
            // admin → enseignants de son école uniquement
            $enseignants = $this->enseignantModel->findEnseignantsByEcole($currentUser['ecole_id']);
        }

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            require_once 'views/classes/create.php';
            return;
        }

        $nom           = trim($_POST['nom'] ?? '');
        $enseignant_id = $_POST['enseignant_id'] ?? null;
        $ecole_id      = $currentUser['role'] === 'super_admin'
                         ? ($_POST['ecole_id'] ?? null)
                         : $currentUser['ecole_id'];

        // 1. Validation
        $nomsAutorises = [
            'Toute Petite Section', 'Petite Section',
            'Moyenne Section', 'Grande Section',
            'CP1', 'CP2', 'CE1', 'CE2', 'CM1', 'CM2'
        ];

        if (empty($nom) || !in_array($nom, $nomsAutorises)) {
            $_SESSION['error'] = 'Nom de classe invalide.';
            header('Location: /gestionecole/classe/create');
            exit;
        }

        if (empty($ecole_id)) {
            $_SESSION['error'] = 'Veuillez choisir une école.';
            header('Location: /gestionecole/classe/create');
            exit;
        }

        // 2. Vérifier doublon dans la même école
        if ($this->classeModel->classeExists($ecole_id, $nom)) {
            $_SESSION['error'] = 'Cette classe existe déjà dans cette école.';
            header('Location: /gestionecole/classe/create');
            exit;
        }

        // 3. Créer la classe
        $this->classeModel->create($ecole_id, $nom, $enseignant_id);

        $_SESSION['success'] = 'Classe créée avec succès.';
        header('Location: /gestionecole/classe/index');
        exit;
    }

    // ─────────────────────────────────────────
    // FORMULAIRE MODIFICATION
    // GET /gestionecole/classe/edit?id=1
    // ─────────────────────────────────────────
    public function edit() {
        Auth::role('super_admin', 'admin');
        $currentUser = Auth::user();
        $id = $_GET['id'] ?? null;

        if (!$id) {
            header('Location: /gestionecole/classe/index');
            exit;
        }

        $classe = $this->classeModel->findById($id);

        if (!$classe) {
            $_SESSION['error'] = 'Classe introuvable.';
            header('Location: /gestionecole/classe/index');
            exit;
        }

        // Admin ne peut pas modifier une classe d'une autre école
        if ($currentUser['role'] === 'admin' &&
            $classe['ecole_id'] != $currentUser['ecole_id']) {
            $_SESSION['error'] = 'Action non autorisée.';
            header('Location: /gestionecole/classe/index');
            exit;
        }

        // Charger les enseignants de l'école de la classe
        $enseignants = $this->enseignantModel->findEnseignantsByEcole($classe['ecole_id']);

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            require_once 'views/classes/edit.php';
            return;
        }

        $nom           = trim($_POST['nom'] ?? '');
        $enseignant_id = $_POST['enseignant_id'] ?? null;

        // 1. Validation
        $nomsAutorises = [
            'Toute Petite Section', 'Petite Section',
            'Moyenne Section', 'Grande Section',
            'CP1', 'CP2', 'CE1', 'CE2', 'CM1', 'CM2'
        ];

        if (empty($nom) || !in_array($nom, $nomsAutorises)) {
            $_SESSION['error'] = 'Nom de classe invalide.';
            header('Location: /gestionecole/classe/edit?id=' . $id);
            exit;
        }

        // 2. Vérifier doublon en excluant la classe actuelle
        if ($this->classeModel->classeExists($classe['ecole_id'], $nom, $id)) {
            $_SESSION['error'] = 'Cette classe existe déjà dans cette école.';
            header('Location: /gestionecole/classe/edit?id=' . $id);
            exit;
        }

        // 3. Modifier la classe
        $this->classeModel->update($id, $nom, $enseignant_id);

        $_SESSION['success'] = 'Classe modifiée avec succès.';
        header('Location: /gestionecole/classe/index');
        exit;
    }

    // ─────────────────────────────────────────
    // SUPPRESSION
    // GET /gestionecole/classe/delete?id=1
    // ─────────────────────────────────────────
    public function delete() {
        Auth::role('super_admin', 'admin');
        $currentUser = Auth::user();
        $id = $_GET['id'] ?? null;

        if (!$id) {
            header('Location: /gestionecole/classe/index');
            exit;
        }

        $classe = $this->classeModel->findById($id);

        if (!$classe) {
            $_SESSION['error'] = 'Classe introuvable.';
            header('Location: /gestionecole/classe/index');
            exit;
        }

        // Admin ne peut pas supprimer une classe d'une autre école
        if ($currentUser['role'] === 'admin' &&
            $classe['ecole_id'] != $currentUser['ecole_id']) {
            $_SESSION['error'] = 'Action non autorisée.';
            header('Location: /gestionecole/classe/index');
            exit;
        }

        $this->classeModel->delete($id);

        $_SESSION['success'] = 'Classe supprimée avec succès.';
        header('Location: /gestionecole/classe/index');
        exit;
    }
}