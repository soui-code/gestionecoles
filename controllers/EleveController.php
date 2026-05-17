<?php
// controllers/EleveController.php

require_once 'models/eleve.php';
require_once 'models/classe.php';
require_once 'middleware/auth.php';

class EleveController {
    private $eleveModel;
    private $classeModel;

    public function __construct() {
        Auth::role('super_admin', 'admin', 'enseignant', 'secretaire');
        $this->eleveModel  = new Eleve();
        $this->classeModel = new Classe();
    }

    // ─────────────────────────────────────────
    // LISTE DES ÉLÈVES
    // GET /gestionecole/eleve/index
    // ─────────────────────────────────────────
    public function index() {
        $currentUser = Auth::user();

        if ($currentUser['role'] === 'super_admin') {
            $eleves = $this->eleveModel->findAll();
        } elseif ($currentUser['role'] === 'enseignant') {
            $classes = $this->classeModel->findByEnseignant($currentUser['id']);
            $eleves  = [];
            foreach ($classes as $classe) {
                $elevesClasse = $this->eleveModel->findByClasse($classe['id']);
                $eleves       = array_merge($eleves, $elevesClasse);
            }
        } else {
            // admin, secretaire
            $eleves = $this->eleveModel->findByEcole($currentUser['ecole_id']);
        }

        require_once 'views/eleve/index.php';
    }

    // ─────────────────────────────────────────
    // ÉLÈVES PAR CLASSE
    // GET /gestionecole/eleve/show?classe_id=1
    // ─────────────────────────────────────────
    public function show() {
        Auth::role('super_admin', 'admin', 'enseignant', 'secretaire');
        $currentUser = Auth::user();
        $classe_id   = $_GET['classe_id'] ?? null;

        if (!$classe_id) {
            header('Location: /gestionecole/eleve/index');
            exit;
        }

        $classe = $this->classeModel->findById($classe_id);

        if (!$classe) {
            $_SESSION['error'] = 'Classe introuvable.';
            header('Location: /gestionecole/eleve/index');
            exit;
        }

        // Admin/secretaire ne peut pas voir une classe d'une autre école
        if (in_array($currentUser['role'], ['admin', 'secretaire']) &&
            $classe['ecole_id'] != $currentUser['ecole_id']) {
            $_SESSION['error'] = 'Action non autorisée.';
            header('Location: /gestionecole/eleve/index');
            exit;
        }

        // Enseignant ne peut voir que ses propres classes
        if ($currentUser['role'] === 'enseignant' &&
            $classe['enseignant_id'] != $currentUser['id']) {
            $_SESSION['error'] = 'Action non autorisée.';
            header('Location: /gestionecole/eleve/index');
            exit;
        }

        $eleves = $this->eleveModel->findByClasse($classe_id);
        require_once 'views/eleve/show.php';
    }

    // ─────────────────────────────────────────
    // FORMULAIRE CRÉATION
    // GET /gestionecole/eleve/create
    // ─────────────────────────────────────────
    public function create() {
        Auth::role('super_admin', 'admin', 'secretaire');
        $currentUser = Auth::user();

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            require_once 'views/eleve/create.php';
            return;
        }

        $matricule       = trim($_POST['matricule'] ?? '');
        $nom             = trim($_POST['nom'] ?? '');
        $prenom          = trim($_POST['prenom'] ?? '');
        $date_naissance  = trim($_POST['date_naissance'] ?? '');
        $lieu_naissance  = trim($_POST['lieu_naissance'] ?? '');
        $domicile        = trim($_POST['domicile'] ?? '');
        $tel_tuteur      = trim($_POST['tel_tuteur'] ?? '');
        $nom_tuteur      = trim($_POST['nom_tuteur'] ?? '');
        $nom_pere        = trim($_POST['nom_pere'] ?? '');
        $nom_mere        = trim($_POST['nom_mere'] ?? '');
        $tel_pere        = trim($_POST['tel_pere'] ?? '');
        $tel_mere        = trim($_POST['tel_mere'] ?? '');
        $profession_pere = trim($_POST['profession_pere'] ?? '');
        $ecole_id        = $currentUser['ecole_id'];

        // 1. Validation
        if (empty($matricule) || empty($nom) || empty($prenom)) {
            $_SESSION['error'] = 'Matricule, nom et prénom sont obligatoires.';
            header('Location: /gestionecole/eleve/create');
            exit;
        }

        // 2. Vérifier doublon matricule
        if ($this->eleveModel->matriculeExists($matricule)) {
            $_SESSION['error'] = 'Ce matricule est déjà utilisé.';
            header('Location: /gestionecole/eleve/create');
            exit;
        }

        // 3. Créer l'élève
        $this->eleveModel->create(
            $ecole_id, $matricule, $nom, $prenom,
            $date_naissance, $lieu_naissance, $domicile,
            $tel_tuteur, $nom_tuteur, $nom_pere, $nom_mere,
            $tel_pere, $tel_mere, $profession_pere
        );

        $_SESSION['success'] = 'Élève ajouté avec succès.';
        header('Location: /gestionecole/eleve/index');
        exit;
    }

    // ─────────────────────────────────────────
    // FORMULAIRE MODIFICATION
    // GET /gestionecole/eleve/edit?id=1
    // ─────────────────────────────────────────
    public function edit() {
        Auth::role('super_admin', 'admin', 'secretaire');
        $currentUser = Auth::user();
        $id = $_GET['id'] ?? null;

        if (!$id) {
            header('Location: /gestionecole/eleve/index');
            exit;
        }

        $eleve = $this->eleveModel->findById($id);

        if (!$eleve) {
            $_SESSION['error'] = 'Élève introuvable.';
            header('Location: /gestionecole/eleve/index');
            exit;
        }

        // Admin/secretaire ne peut pas modifier un élève d'une autre école
        if (in_array($currentUser['role'], ['admin', 'secretaire']) &&
            $eleve['ecole_id'] != $currentUser['ecole_id']) {
            $_SESSION['error'] = 'Action non autorisée.';
            header('Location: /gestionecole/eleve/index');
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            require_once 'views/eleve/edit.php';
            return;
        }

        $matricule       = trim($_POST['matricule'] ?? '');
        $nom             = trim($_POST['nom'] ?? '');
        $prenom          = trim($_POST['prenom'] ?? '');
        $date_naissance  = trim($_POST['date_naissance'] ?? '');
        $lieu_naissance  = trim($_POST['lieu_naissance'] ?? '');
        $domicile        = trim($_POST['domicile'] ?? '');
        $tel_tuteur      = trim($_POST['tel_tuteur'] ?? '');
        $nom_tuteur      = trim($_POST['nom_tuteur'] ?? '');
        $nom_pere        = trim($_POST['nom_pere'] ?? '');
        $nom_mere        = trim($_POST['nom_mere'] ?? '');
        $tel_pere        = trim($_POST['tel_pere'] ?? '');
        $tel_mere        = trim($_POST['tel_mere'] ?? '');
        $profession_pere = trim($_POST['profession_pere'] ?? '');

        // 1. Validation
        if (empty($matricule) || empty($nom) || empty($prenom)) {
            $_SESSION['error'] = 'Matricule, nom et prénom sont obligatoires.';
            header('Location: /gestionecole/eleve/edit?id=' . $id);
            exit;
        }

        // 2. Vérifier doublon matricule
        if ($this->eleveModel->matriculeExists($matricule, $id)) {
            $_SESSION['error'] = 'Ce matricule est déjà utilisé.';
            header('Location: /gestionecole/eleve/edit?id=' . $id);
            exit;
        }

        // 3. Modifier l'élève
        $this->eleveModel->update(
            $id, $matricule, $nom, $prenom,
            $date_naissance, $lieu_naissance, $domicile,
            $tel_tuteur, $nom_tuteur, $nom_pere, $nom_mere,
            $tel_pere, $tel_mere, $profession_pere
        );

        $_SESSION['success'] = 'Élève modifié avec succès.';
        header('Location: /gestionecole/eleve/index');
        exit;
    }

    // ─────────────────────────────────────────
    // SUPPRESSION
    // GET /gestionecole/eleve/delete?id=1
    // ─────────────────────────────────────────
    public function delete() {
        Auth::role('super_admin', 'admin', 'secretaire');
        $currentUser = Auth::user();
        $id = $_GET['id'] ?? null;

        if (!$id) {
            header('Location: /gestionecole/eleve/index');
            exit;
        }

        $eleve = $this->eleveModel->findById($id);

        if (!$eleve) {
            $_SESSION['error'] = 'Élève introuvable.';
            header('Location: /gestionecole/eleve/index');
            exit;
        }

        // Admin/secretaire ne peut pas supprimer un élève d'une autre école
        if (in_array($currentUser['role'], ['admin', 'secretaire']) &&
            $eleve['ecole_id'] != $currentUser['ecole_id']) {
            $_SESSION['error'] = 'Action non autorisée.';
            header('Location: /gestionecole/eleve/index');
            exit;
        }

        $this->eleveModel->delete($id);

        $_SESSION['success'] = 'Élève supprimé avec succès.';
        header('Location: /gestionecole/eleve/index');
        exit;
    }
    // ─────────────────────────────────────────
// LISTE PAR CLASSE (imprimable)
// GET /gestionecole/eleve/listeClasse?classe_id=1
// ─────────────────────────────────────────
public function listeClasse() {
    Auth::role('super_admin', 'admin', 'enseignant', 'secretaire');
    $currentUser = Auth::user();
    $classe_id   = $_GET['classe_id'] ?? null;

    if (!$classe_id) {
        header('Location: /gestionecole/classe/index');
        exit;
    }

    $classe      = $this->classeModel->findById($classe_id);
    $anneeActive = (new Annee())->findActive();

    if (!$classe) {
        $_SESSION['error'] = 'Classe introuvable.';
        header('Location: /gestionecole/classe/index');
        exit;
    }

    // Vérification droits
    if (in_array($currentUser['role'], ['admin', 'secretaire']) &&
        $classe['ecole_id'] != $currentUser['ecole_id']) {
        $_SESSION['error'] = 'Action non autorisée.';
        header('Location: /gestionecole/classe/index');
        exit;
    }

    if ($currentUser['role'] === 'enseignant' &&
        $classe['enseignant_id'] != $currentUser['id']) {
        $_SESSION['error'] = 'Action non autorisée.';
        header('Location: /gestionecole/classe/index');
        exit;
    }

    $eleves = $this->eleveModel->findByClasse($classe_id);

    require_once 'views/eleve/liste_classe.php';
}
}