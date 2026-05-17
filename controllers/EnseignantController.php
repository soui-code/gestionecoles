<?php
// controllers/EnseignantController.php

require_once 'models/Enseignant.php';
require_once 'models/Classe.php';
require_once 'models/Eleve.php';
require_once 'middleware/auth.php';

class EnseignantController {
    private $enseignantModel;
    private $classeModel;
    private $eleveModel;

    public function __construct() {
        Auth::role('super_admin', 'admin', 'enseignant', 'secretaire');
        $this->enseignantModel = new Enseignant();
        $this->classeModel     = new Classe();
        $this->eleveModel      = new Eleve();
    }

    // ─────────────────────────────────────────
    // DASHBOARD ENSEIGNANT
    // GET /gestionecole/enseignant/dashboard
    // ─────────────────────────────────────────
    public function dashboard() {
        Auth::role('enseignant');
        $currentUser = Auth::user();

        // Récupérer les classes de l'enseignant
        $classes = $this->classeModel->findByEnseignant($currentUser['id']);

        // Pour chaque classe récupérer les élèves
        $classesAvecEleves = [];
        foreach ($classes as $classe) {
            $eleves = $this->eleveModel->findByClasse($classe['id']);
            $classesAvecEleves[] = [
                'classe' => $classe,
                'eleves' => $eleves,
                'total'  => count($eleves)
            ];
        }

        require_once 'views/enseignant/dashboard.php';
    }

    // ─────────────────────────────────────────
    // LISTE DES ENSEIGNANTS
    // GET /gestionecole/enseignant/index
    // ─────────────────────────────────────────
    public function index() {
        Auth::role('super_admin', 'admin');
        $currentUser = Auth::user();

        if ($currentUser['role'] === 'super_admin') {
            $enseignants = $this->enseignantModel->findAll();
        } else {
            $enseignants = $this->enseignantModel->findByEcole($currentUser['ecole_id']);
        }

        require_once 'views/enseignant/index.php';
    }

    // ─────────────────────────────────────────
    // FORMULAIRE CRÉATION
    // GET /gestionecole/enseignant/create
    // ─────────────────────────────────────────
    public function create() {
        Auth::role('super_admin', 'admin');
        $currentUser = Auth::user();

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            require_once 'views/enseignant/create.php';
            return;
        }

        $nom      = trim($_POST['nom'] ?? '');
        $prenom   = trim($_POST['prenom'] ?? '');
        $email    = trim($_POST['email'] ?? '');
        $tel      = trim($_POST['tel'] ?? '');
        $password = trim($_POST['password'] ?? '');
        $role     = trim($_POST['role'] ?? 'enseignant');
        $ecole_id = $currentUser['ecole_id'];

        // 1. Validation
        if (empty($nom) || empty($email) || empty($password)) {
            $_SESSION['error'] = 'Nom, email et mot de passe sont obligatoires.';
            header('Location: /gestionecole/enseignant/create');
            exit;
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $_SESSION['error'] = 'Email invalide.';
            header('Location: /gestionecole/enseignant/create');
            exit;
        }

        if (strlen($password) < 6) {
            $_SESSION['error'] = 'Le mot de passe doit contenir au moins 6 caractères.';
            header('Location: /gestionecole/enseignant/create');
            exit;
        }

        if (!in_array($role, ['enseignant', 'secretaire'])) {
            $_SESSION['error'] = 'Rôle invalide.';
            header('Location: /gestionecole/enseignant/create');
            exit;
        }

        // 2. Vérifier doublon email
        if ($this->enseignantModel->emailExists($email)) {
            $_SESSION['error'] = 'Cet email est déjà utilisé.';
            header('Location: /gestionecole/enseignant/create');
            exit;
        }

        // 3. Vérifier doublon tel
        if (!empty($tel) && $this->enseignantModel->telExists($tel)) {
            $_SESSION['error'] = 'Ce numéro de téléphone est déjà utilisé.';
            header('Location: /gestionecole/enseignant/create');
            exit;
        }

        // 4. Créer l'enseignant ou secrétaire
        $this->enseignantModel->create($ecole_id, $nom, $prenom, $email, $tel, $password, $role);

        $_SESSION['success'] = ucfirst($role) . ' créé(e) avec succès.';
        header('Location: /gestionecole/enseignant/index');
        exit;
    }

    // ─────────────────────────────────────────
    // FORMULAIRE MODIFICATION
    // GET /gestionecole/enseignant/edit?id=1
    // ─────────────────────────────────────────
    public function edit() {
        Auth::role('super_admin', 'admin');
        $currentUser = Auth::user();
        $id = $_GET['id'] ?? null;

        if (!$id) {
            header('Location: /gestionecole/enseignant/index');
            exit;
        }

        $enseignant = $this->enseignantModel->findById($id);

        if (!$enseignant) {
            $_SESSION['error'] = 'Enseignant introuvable.';
            header('Location: /gestionecole/enseignant/index');
            exit;
        }

        // Admin ne peut pas modifier un enseignant d'une autre école
        if ($currentUser['role'] === 'admin' &&
            $enseignant['ecole_id'] != $currentUser['ecole_id']) {
            $_SESSION['error'] = 'Action non autorisée.';
            header('Location: /gestionecole/enseignant/index');
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            require_once ROOT . 'views/enseignants/edit.php';
            return;
        }

        $nom    = trim($_POST['nom'] ?? '');
        $prenom = trim($_POST['prenom'] ?? '');
        $email  = trim($_POST['email'] ?? '');
        $tel    = trim($_POST['tel'] ?? '');
        $role   = trim($_POST['role'] ?? 'enseignant');
        $password = trim($_POST['password'] ?? '');

        // 1. Validation
        if (empty($nom) || empty($email)) {
            $_SESSION['error'] = 'Nom et email sont obligatoires.';
            header('Location: /gestionecole/enseignant/edit?id=' . $id);
            exit;
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $_SESSION['error'] = 'Email invalide.';
            header('Location: /gestionecole/enseignant/edit?id=' . $id);
            exit;
        }

        if (!in_array($role, ['enseignant', 'secretaire'])) {
            $_SESSION['error'] = 'Rôle invalide.';
            header('Location: /gestionecole/enseignant/edit?id=' . $id);
            exit;
        }

        // 2. Vérifier doublon email
        if ($this->enseignantModel->emailExists($email, $id)) {
            $_SESSION['error'] = 'Cet email est déjà utilisé.';
            header('Location: /gestionecole/enseignant/edit?id=' . $id);
            exit;
        }

        // 3. Vérifier doublon tel
        if (!empty($tel) && $this->enseignantModel->telExists($tel, $id)) {
            $_SESSION['error'] = 'Ce numéro de téléphone est déjà utilisé.';
            header('Location: /gestionecole/enseignant/edit?id=' . $id);
            exit;
        }

        // 4. Modifier l'enseignant
        $this->enseignantModel->update($id, $nom, $prenom, $email, $tel, $role);

        // 5. Mettre à jour le mot de passe si renseigné
        if (!empty($password)) {
            if (strlen($password) < 6) {
                $_SESSION['error'] = 'Le mot de passe doit contenir au moins 6 caractères.';
                header('Location: /gestionecole/enseignant/edit?id=' . $id);
                exit;
            }
            $this->enseignantModel->updatePassword($id, $password);
        }

        $_SESSION['success'] = 'Modifié(e) avec succès.';
        header('Location: /gestionecole/enseignant/index');
        exit;
    }

    // ─────────────────────────────────────────
    // SUPPRESSION
    // GET /gestionecole/enseignant/delete?id=1
    // ─────────────────────────────────────────
    public function delete() {
        Auth::role('super_admin', 'admin');
        $currentUser = Auth::user();
        $id = $_GET['id'] ?? null;

        if (!$id) {
            header('Location: /gestionecole/enseignant/index');
            exit;
        }

        $enseignant = $this->enseignantModel->findById($id);

        if (!$enseignant) {
            $_SESSION['error'] = 'Enseignant introuvable.';
            header('Location: /gestionecole/enseignant/index');
            exit;
        }

        // Admin ne peut pas supprimer un enseignant d'une autre école
        if ($currentUser['role'] === 'admin' &&
            $enseignant['ecole_id'] != $currentUser['ecole_id']) {
            $_SESSION['error'] = 'Action non autorisée.';
            header('Location: /gestionecole/enseignant/index');
            exit;
        }

        // Empêcher de se supprimer soi-même
        if ($enseignant['id'] == $currentUser['id']) {
            $_SESSION['error'] = 'Vous ne pouvez pas supprimer votre propre compte.';
            header('Location: /gestionecole/enseignant/index');
            exit;
        }

        $this->enseignantModel->delete($id);

        $_SESSION['success'] = 'Supprimé(e) avec succès.';
        header('Location: /gestionecole/enseignant/index');
        exit;
    }
}