<?php
// controllers/SuperAdminController.php

require_once 'models/ecole.php';
require_once 'models/user.php';
require_once 'models/eleve.php';
require_once 'models/enseignant.php';
require_once 'middleware/auth.php';

class SuperAdminController {
    private $ecoleModel;
    private $userModel;
    private $eleveModel;
    private $enseignantModel;

    public function __construct() {
        Auth::role('super_admin');
        $this->ecoleModel      = new Ecole();
        $this->userModel       = new User();
        $this->eleveModel      = new Eleve();
        $this->enseignantModel = new Enseignant();
    }

    // ─────────────────────────────────────────
    // DASHBOARD
    // GET /gestionecole/superAdmin/dashboard
    // ─────────────────────────────────────────
    public function dashboard() {
        // Stats globales
        $stats = [
            'total_ecoles'      => $this->ecoleModel->count(),
            'total_admins'      => $this->userModel->countByRole('admin'),
            'total_enseignants' => $this->enseignantModel->countAll('enseignant'),
            'total_secretaires' => $this->enseignantModel->countAll('secretaire'),
            'total_eleves'      => $this->eleveModel->count(),
        ];

        // Liste des écoles avec stats
        $ecoles = $this->ecoleModel->findAllWithStats();

        require_once 'views/super_admin/dashboard.php';
    }
}