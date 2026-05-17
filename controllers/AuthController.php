<?php
// controllers/AuthController.php

require_once 'models/User.php';
require_once 'models/Enseignant.php';

class AuthController {
    private $userModel;
    private $enseignantModel;

    public function __construct() {
        $this->userModel       = new User();
        $this->enseignantModel = new Enseignant();
    }

    // ─────────────────────────────────────────
    // LOGIN
    // GET /gestionecole/auth/login
    // ─────────────────────────────────────────
    public function login() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            require_once 'views/auth/login.php';
            return;
        }

        $email    = trim($_POST['email'] ?? '');
        $password = trim($_POST['password'] ?? '');

        // 1. Validation des champs
        if (empty($email) || empty($password)) {
            $_SESSION['error'] = 'Email et mot de passe sont obligatoires.';
            header('Location: /gestionecole/auth/login');
            exit;
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $_SESSION['error'] = 'Email invalide.';
            header('Location: /gestionecole/auth/login');
            exit;
        }

        // 2. Chercher dans users (super_admin, admin)
        $user = $this->userModel->findByEmail($email);

        if ($user) {
            // 3. Vérifier le mot de passe
            if (!$this->userModel->verifyPassword($password, $user['password'])) {
                $_SESSION['error'] = 'Identifiants incorrects.';
                header('Location: /gestionecole/auth/login');
                exit;
            }

            // 4. Stocker en session
            $clean = $this->userModel->sanitize($user);
            $this->setSession($clean, $clean['role']);

            // 5. Rediriger selon le rôle
            $this->redirectByRole($clean['role']);
        }

        // 6. Chercher dans enseignants (enseignant, secretaire)
        $enseignant = $this->enseignantModel->findByEmail($email);

        if ($enseignant) {
            // 7. Vérifier le mot de passe
            if (!$this->enseignantModel->verifyPassword($password, $enseignant['password'])) {
                $_SESSION['error'] = 'Identifiants incorrects.';
                header('Location: /gestionecole/auth/login');
                exit;
            }

            // 8. Stocker en session
            $clean = $this->enseignantModel->sanitize($enseignant);
            $this->setSession($clean, $clean['role']);

            // 9. Rediriger selon le rôle
            $this->redirectByRole($clean['role']);
        }

        // 10. Aucun utilisateur trouvé
        $_SESSION['error'] = 'Identifiants incorrects.';
        header('Location: /gestionecole/auth/login');
        exit;
    }

    // ─────────────────────────────────────────
    // LOGOUT
    // GET /gestionecole/auth/logout
    // ─────────────────────────────────────────
    public function logout() {
        session_unset();
        session_destroy();
        header('Location: /gestionecole/auth/login');
        exit;
    }

    // ─────────────────────────────────────────
    // STOCKER EN SESSION
    // ─────────────────────────────────────────
    private function setSession($user, $role) {
        $_SESSION['user_id']   = $user['id'];
        $_SESSION['user_nom']  = $user['nom'];
        $_SESSION['user_role'] = $role;
        $_SESSION['ecole_id']  = $user['ecole_id'];
        $_SESSION['ecole_nom'] = $user['ecole_nom'] ?? null;
    }

    // ─────────────────────────────────────────
    // REDIRECTION SELON LE RÔLE
    // ─────────────────────────────────────────
    private function redirectByRole($role) {
        switch ($role) {
            case 'super_admin':
                header('Location: /gestionecole/superAdmin/dashboard');
                break;
            case 'admin':
                header('Location: /gestionecole/admin/dashboard');
                break;
            case 'secretaire':
                header('Location: /gestionecole/secretaire/dashboard');
                break;
            case 'enseignant':
                header('Location: /gestionecole/enseignant/dashboard');
                break;
            default:
                header('Location: /gestionecole/auth/login');
                break;
        }
        exit;
    }
}