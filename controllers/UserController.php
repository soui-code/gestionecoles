<?php
// controllers/UserController.php

require_once 'models/User.php';
require_once 'models/Ecole.php';
require_once 'middleware/auth.php';

class UserController {
    private $userModel;

    public function __construct() {
        Auth::role('super_admin', 'admin');
        $this->userModel = new User();
    }

    // ─────────────────────────────────────────
    // LISTE DES USERS
    // GET /gestionecole/user/index
    // ─────────────────────────────────────────
    public function index() {
        $currentUser = Auth::user();

        if ($currentUser['role'] === 'super_admin') {
            $users = $this->userModel->findAll();
        } else {
            $users = $this->userModel->findByEcole($currentUser['ecole_id']);
        }

        require_once 'views/users/index.php';
    }

    // ─────────────────────────────────────────
    // FORMULAIRE CRÉATION
    // GET /gestionecole/user/create
    // ─────────────────────────────────────────
    public function create() {
        $currentUser = Auth::user();

        // Charger les écoles si super_admin
        $ecoles = [];
        if ($currentUser['role'] === 'super_admin') {
            $ecoleModel = new Ecole();
            $ecoles     = $ecoleModel->findAll();
        }

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            require_once 'views/users/create.php';
            return;
        }

        $nom      = trim($_POST['nom'] ?? '');
        $email    = trim($_POST['email'] ?? '');
        $password = trim($_POST['password'] ?? '');
        $role     = trim($_POST['role'] ?? '');
        $ecole_id = $currentUser['role'] === 'super_admin'
                    ? ($_POST['ecole_id'] ?? null)
                    : $currentUser['ecole_id'];

        // 1. Validation
        if (empty($nom) || empty($email) || empty($password) || empty($role)) {
            $_SESSION['error'] = 'Tous les champs obligatoires doivent être remplis.';
            header('Location: /gestionecole/user/create');
            exit;
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $_SESSION['error'] = 'Email invalide.';
            header('Location: /gestionecole/user/create');
            exit;
        }

        if (strlen($password) < 6) {
            $_SESSION['error'] = 'Le mot de passe doit contenir au moins 6 caractères.';
            header('Location: /gestionecole/user/create');
            exit;
        }

        // Rôles autorisés selon l'acteur connecté
        $rolesAutorises = $currentUser['role'] === 'super_admin'
            ? ['super_admin', 'admin']
            : ['admin'];

        if (!in_array($role, $rolesAutorises)) {
            $_SESSION['error'] = 'Rôle invalide.';
            header('Location: /gestionecole/user/create');
            exit;
        }

        // 2. Vérifier doublon email
        if ($this->userModel->emailExists($email)) {
            $_SESSION['error'] = 'Cet email est déjà utilisé.';
            header('Location: /gestionecole/user/create');
            exit;
        }

        // 3. Créer le user
        $this->userModel->create($ecole_id, $nom, $email, $password, $role);

        $_SESSION['success'] = 'Utilisateur créé avec succès.';
        header('Location: /gestionecole/user/index');
        exit;
    }

    // ─────────────────────────────────────────
    // FORMULAIRE MODIFICATION
    // GET /gestionecole/user/edit?id=1
    // ─────────────────────────────────────────
    public function edit() {
        $currentUser = Auth::user();
        $id = $_GET['id'] ?? null;

        if (!$id) {
            header('Location: /gestionecole/user/index');
            exit;
        }

        $user = $this->userModel->findById($id);

        if (!$user) {
            $_SESSION['error'] = 'Utilisateur introuvable.';
            header('Location: /gestionecole/user/index');
            exit;
        }

        // Admin ne peut pas modifier un user d'une autre école
        if ($currentUser['role'] === 'admin' &&
            $user['ecole_id'] != $currentUser['ecole_id']) {
            $_SESSION['error'] = 'Action non autorisée.';
            header('Location: /gestionecole/user/index');
            exit;
        }

        // Charger les écoles si super_admin
        $ecoles = [];
        if ($currentUser['role'] === 'super_admin') {
            $ecoleModel = new Ecole();
            $ecoles     = $ecoleModel->findAll();
        }

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            require_once ROOT . 'views/users/edit.php';
            return;
        }

        $nom      = trim($_POST['nom'] ?? '');
        $email    = trim($_POST['email'] ?? '');
        $password = trim($_POST['password'] ?? '');
        $role     = trim($_POST['role'] ?? '');
        $ecole_id = $currentUser['role'] === 'super_admin'
                    ? ($_POST['ecole_id'] ?? null)
                    : $currentUser['ecole_id'];

        // 1. Validation
        if (empty($nom) || empty($email) || empty($role)) {
            $_SESSION['error'] = 'Tous les champs obligatoires doivent être remplis.';
            header('Location: /gestionecole/user/edit?id=' . $id);
            exit;
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $_SESSION['error'] = 'Email invalide.';
            header('Location: /gestionecole/user/edit?id=' . $id);
            exit;
        }

        // Rôles autorisés
        $rolesAutorises = $currentUser['role'] === 'super_admin'
            ? ['super_admin', 'admin']
            : ['admin'];

        if (!in_array($role, $rolesAutorises)) {
            $_SESSION['error'] = 'Rôle invalide.';
            header('Location: /gestionecole/user/edit?id=' . $id);
            exit;
        }

        // 2. Vérifier doublon email
        if ($this->userModel->emailExists($email, $id)) {
            $_SESSION['error'] = 'Cet email est déjà utilisé.';
            header('Location: /gestionecole/user/edit?id=' . $id);
            exit;
        }

        // 3. Modifier le user
        $this->userModel->update($id, $ecole_id, $nom, $email, $role);

        // 4. Mettre à jour le mot de passe si renseigné
        if (!empty($password)) {
            if (strlen($password) < 6) {
                $_SESSION['error'] = 'Le mot de passe doit contenir au moins 6 caractères.';
                header('Location: /gestionecole/user/edit?id=' . $id);
                exit;
            }
            $this->userModel->updatePassword($id, $password);
        }

        $_SESSION['success'] = 'Utilisateur modifié avec succès.';
        header('Location: /gestionecole/user/index');
        exit;
    }

    // ─────────────────────────────────────────
    // SUPPRESSION
    // GET /gestionecole/user/delete?id=1
    // ─────────────────────────────────────────
    public function delete() {
        $currentUser = Auth::user();
        $id = $_GET['id'] ?? null;

        if (!$id) {
            header('Location: /gestionecole/user/index');
            exit;
        }

        $user = $this->userModel->findById($id);

        if (!$user) {
            $_SESSION['error'] = 'Utilisateur introuvable.';
            header('Location: /gestionecole/user/index');
            exit;
        }

        // Admin ne peut pas supprimer un user d'une autre école
        if ($currentUser['role'] === 'admin' &&
            $user['ecole_id'] != $currentUser['ecole_id']) {
            $_SESSION['error'] = 'Action non autorisée.';
            header('Location: /gestionecole/user/index');
            exit;
        }

        // Empêcher de se supprimer soi-même
        if ($user['id'] == $currentUser['id']) {
            $_SESSION['error'] = 'Vous ne pouvez pas supprimer votre propre compte.';
            header('Location: /gestionecole/user/index');
            exit;
        }

        $this->userModel->delete($id);

        $_SESSION['success'] = 'Utilisateur supprimé avec succès.';
        header('Location: /gestionecole/user/index');
        exit;
    }
}