<?php

require_once 'models/ecole.php';
require_once 'middleware/auth.php';

class EcoleController {
    private $ecoleModel;

    public function __construct() {
        Auth::role('super_admin');
        $this->ecoleModel = new Ecole();
    }

    // ─────────────────────────────────────────
    // LISTE DES ÉCOLES
    // GET /gestionecole/ecole/index
    // ─────────────────────────────────────────
    public function index() {
        $ecoles = $this->ecoleModel->findAll();
        require_once 'views/ecoles/index.php';
    }

    // ─────────────────────────────────────────
    // FORMULAIRE CRÉATION
    // GET /gestionecole/ecole/create
    // ─────────────────────────────────────────
    public function create() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            require_once  'views/ecoles/create.php';
            return;
        }

        $nom       = trim($_POST['nom'] ?? '');
        $adresse   = trim($_POST['adresse'] ?? '');
        $telephone = trim($_POST['telephone'] ?? '');
        $email     = trim($_POST['email'] ?? '');

        // 1. Validation
        if (empty($nom)) {
            $_SESSION['error'] = 'Le nom de l\'école est obligatoire.';
            header('Location: /gestionecole/ecole/create');
            exit;
        }

        if (!empty($email) && !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $_SESSION['error'] = 'Email invalide.';
            header('Location: /gestionecole/ecole/create');
            exit;
        }

        // 2. Vérifier doublon email
        if (!empty($email) && $this->ecoleModel->emailExists($email)) {
            $_SESSION['error'] = 'Cet email est déjà utilisé.';
            header('Location: /gestionecole/ecole/create');
            exit;
        }

        // 3. Créer l'école
        $this->ecoleModel->create($nom, $adresse, $telephone, $email);

        $_SESSION['success'] = 'École créée avec succès.';
        header('Location: /gestionecole/ecole/index');
        exit;
    }

    // ─────────────────────────────────────────
    // FORMULAIRE MODIFICATION
    // GET /gestionecole/ecole/edit?id=1
    // ─────────────────────────────────────────
    public function edit() {
        $id = $_GET['id'] ?? null;

        if (!$id) {
            header('Location: /gestionecole/ecole/index');
            exit;
        }

        $ecole = $this->ecoleModel->findById($id);

        if (!$ecole) {
            $_SESSION['error'] = 'École introuvable.';
            header('Location: /gestionecole/ecole/index');
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            require_once 'views/ecoles/edit.php';
            return;
        }

        $nom       = trim($_POST['nom'] ?? '');
        $adresse   = trim($_POST['adresse'] ?? '');
        $telephone = trim($_POST['telephone'] ?? '');
        $email     = trim($_POST['email'] ?? '');

        // 1. Validation
        if (empty($nom)) {
            $_SESSION['error'] = 'Le nom de l\'école est obligatoire.';
            header('Location: /gestionecole/ecole/edit?id=' . $id);
            exit;
        }

        if (!empty($email) && !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $_SESSION['error'] = 'Email invalide.';
            header('Location: /gestionecole/ecole/edit?id=' . $id);
            exit;
        }

        // 2. Vérifier doublon email (en excluant l'école actuelle)
        if (!empty($email) && $this->ecoleModel->emailExists($email, $id)) {
            $_SESSION['error'] = 'Cet email est déjà utilisé.';
            header('Location: /gestionecole/ecole/edit?id=' . $id);
            exit;
        }

        // 3. Modifier l'école
        $this->ecoleModel->update($id, $nom, $adresse, $telephone, $email);

        $_SESSION['success'] = 'École modifiée avec succès.';
        header('Location: /gestionecole/ecole/index');
        exit;
    }

    // ─────────────────────────────────────────
    // SUPPRESSION
    // GET /gestionecole/ecole/delete?id=1
    // ─────────────────────────────────────────
    public function delete() {
        $id = $_GET['id'] ?? null;

        if (!$id) {
            header('Location: /gestionecole/ecole/index');
            exit;
        }

        $ecole = $this->ecoleModel->findById($id);

        if (!$ecole) {
            $_SESSION['error'] = 'École introuvable.';
            header('Location: /gestionecole/ecole/index');
            exit;
        }

        $this->ecoleModel->delete($id);

        $_SESSION['success'] = 'École supprimée avec succès.';
        header('Location: /gestionecole/ecole/index');
        exit;
    }
}