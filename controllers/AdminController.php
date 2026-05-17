<?php
// controllers/AdminController.php

require_once 'models/ecole.php';
require_once 'models/user.php';
require_once 'models/eleve.php';
require_once 'models/classe.php';
require_once 'models/enseignant.php';
require_once 'middleware/auth.php';

class AdminController {
    private $ecoleModel;
    private $userModel;
    private $eleveModel;
    private $classeModel;
    private $enseignantModel;

    public function __construct() {
        Auth::role('admin');
        $this->ecoleModel      = new Ecole();
        $this->userModel       = new User();
        $this->eleveModel      = new Eleve();
        $this->classeModel     = new Classe();
        $this->enseignantModel = new Enseignant();
    }

    // ─────────────────────────────────────────
    // DASHBOARD
    // GET /gestionecole/admin/dashboard
    // ─────────────────────────────────────────
    public function dashboard() {
        $currentUser = Auth::user();
        $ecole_id    = $currentUser['ecole_id'];

        // Stats de l'école
        $stats = [
            'total_classes'     => $this->classeModel->countByEcole($ecole_id),
            'total_eleves'      => $this->eleveModel->countByEcole($ecole_id),
            'total_enseignants' => $this->enseignantModel->countByEcole($ecole_id),
            'total_secretaires' => $this->enseignantModel->countSecretairesByEcole($ecole_id),
        ];

        // Infos de l'école
        $ecole = $this->ecoleModel->findById($ecole_id);

        // Liste des classes
        $classes = $this->classeModel->findByEcole($ecole_id);

        require_once 'views/admin/dashboard.php';
    }
}