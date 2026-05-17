<?php
// controllers/InscriptionController.php

require_once 'models/inscription.php';
require_once 'models/eleve.php';
require_once 'models/classe.php';
require_once 'models/annee.php';
require_once 'models/paiement.php';
require_once 'config/database.php';
require_once 'middleware/auth.php';

class InscriptionController {
    private $db;
    private $inscriptionModel;
    private $eleveModel;
    private $classeModel;
    private $anneeModel;
    private $paiementModel;

    public function __construct() {
        Auth::role('super_admin', 'admin', 'secretaire');
        $database               = new Database();
        $this->db               = $database->getConnection();
        $this->inscriptionModel = new Inscription();
        $this->eleveModel       = new Eleve();
        $this->classeModel      = new Classe();
        $this->anneeModel       = new Annee();
        $this->paiementModel    = new Paiement();
    }

    // ─────────────────────────────────────────
    // LISTE DES INSCRIPTIONS
    // GET /gestionecole/inscription/index
    // ─────────────────────────────────────────
    public function index() {
        $currentUser = Auth::user();
        $anneeActive = $this->anneeModel->findActive();

        if (!$anneeActive) {
            $_SESSION['error'] = 'Aucune année scolaire active.';
            header('Location: /gestionecole/annee/index');
            exit;
        }

        if ($currentUser['role'] === 'super_admin') {
            $inscriptions = $this->inscriptionModel->findAll();
        } else {
            $inscriptions = $this->inscriptionModel->findByAnnee(
                $currentUser['ecole_id'],
                $anneeActive['id']
            );
        }

        require_once 'views/inscriptions/index.php';
    }

    // ─────────────────────────────────────────
    // CHOIX INSCRIPTION / RÉINSCRIPTION
    // GET /gestionecole/inscription/choix
    // ─────────────────────────────────────────
    public function choix() {
        $anneeActive = $this->anneeModel->findActive();

        if (!$anneeActive) {
            $_SESSION['error'] = 'Aucune année scolaire active.';
            header('Location: /gestionecole/annee/index');
            exit;
        }

        unset($_SESSION['inscription_data']);
        require_once 'views/inscriptions/choix.php';
    }

    // ─────────────────────────────────────────
    // ÉTAPE 1 — NOUVELLE INSCRIPTION
    // GET  /gestionecole/inscription/create
    // POST /gestionecole/inscription/create
    // ─────────────────────────────────────────
    public function create() {
        $currentUser = Auth::user();
        $anneeActive = $this->anneeModel->findActive();
        $classes     = $this->classeModel->findByEcole($currentUser['ecole_id']);

        if (!$anneeActive) {
            $_SESSION['error'] = 'Aucune année scolaire active.';
            header('Location: /gestionecole/annee/index');
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            require_once 'views/inscriptions/create.php';
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
        $classe_id       = $_POST['classe_id'] ?? null;

        // 1. Validation
        if (empty($matricule) || empty($nom) || empty($prenom)) {
            $_SESSION['error'] = 'Matricule, nom et prénom sont obligatoires.';
            header('Location: /gestionecole/inscription/create');
            exit;
        }

        if (empty($classe_id)) {
            $_SESSION['error'] = 'Veuillez choisir une classe.';
            header('Location: /gestionecole/inscription/create');
            exit;
        }

        // 2. Vérifier doublon matricule
        if ($this->eleveModel->matriculeExists($matricule)) {
            $_SESSION['error'] = 'Ce matricule est déjà utilisé.';
            header('Location: /gestionecole/inscription/create');
            exit;
        }

        // 3. Récupérer montant scolarité
        $montantScolarite = $this->inscriptionModel->getMontantScolarite(
            $classe_id,
            $anneeActive['id'],
            $currentUser['ecole_id']
        );

        // 4. Stocker en session
        $_SESSION['inscription_data'] = [
            'type'              => 'inscription',
            'ecole_id'          => $currentUser['ecole_id'],
            'classe_id'         => $classe_id,
            'annee_id'          => $anneeActive['id'],
            'annee_libelle'     => $anneeActive['libelle'],
            'montant_scolarite' => $montantScolarite,
            'eleve' => [
                'matricule'       => $matricule,
                'nom'             => $nom,
                'prenom'          => $prenom,
                'date_naissance'  => $date_naissance,
                'lieu_naissance'  => $lieu_naissance,
                'domicile'        => $domicile,
                'tel_tuteur'      => $tel_tuteur,
                'nom_tuteur'      => $nom_tuteur,
                'nom_pere'        => $nom_pere,
                'nom_mere'        => $nom_mere,
                'tel_pere'        => $tel_pere,
                'tel_mere'        => $tel_mere,
                'profession_pere' => $profession_pere,
            ]
        ];

        header('Location: /gestionecole/inscription/paiement');
        exit;
    }

    // ─────────────────────────────────────────
    // ÉTAPE 1 — RÉINSCRIPTION
    // GET  /gestionecole/inscription/reinscription
    // POST /gestionecole/inscription/reinscription
    // ─────────────────────────────────────────
    public function reinscription() {
        $currentUser = Auth::user();
        $anneeActive = $this->anneeModel->findActive();
        $classes     = $this->classeModel->findByEcole($currentUser['ecole_id']);
        $eleve       = null;

        if (!$anneeActive) {
            $_SESSION['error'] = 'Aucune année scolaire active.';
            header('Location: /gestionecole/annee/index');
            exit;
        }

        // Recherche élève par matricule
        if (isset($_GET['matricule']) && !empty($_GET['matricule'])) {
            $eleve = $this->eleveModel->findByMatricule(trim($_GET['matricule']));

            if (!$eleve) {
                $_SESSION['error'] = 'Aucun élève trouvé avec ce matricule.';
            } elseif ($eleve['ecole_id'] != $currentUser['ecole_id']) {
                $_SESSION['error'] = 'Cet élève n\'appartient pas à votre école.';
                $eleve = null;
            } elseif ($this->inscriptionModel->dejaInscrit($eleve['id'], $anneeActive['id'])) {
                $_SESSION['error'] = 'Cet élève est déjà inscrit pour l\'année ' . $anneeActive['libelle'];
                $eleve = null;
            }
        }

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            require_once 'views/inscriptions/reinscription.php';
            return;
        }

        $eleve_id  = $_POST['eleve_id'] ?? null;
        $classe_id = $_POST['classe_id'] ?? null;

        // 1. Validation
        if (empty($eleve_id) || empty($classe_id)) {
            $_SESSION['error'] = 'Veuillez sélectionner un élève et une classe.';
            header('Location: /gestionecole/inscription/reinscription');
            exit;
        }

        // 2. Récupérer l'élève
        $eleve = $this->eleveModel->findById($eleve_id);

        if (!$eleve) {
            $_SESSION['error'] = 'Élève introuvable.';
            header('Location: /gestionecole/inscription/reinscription');
            exit;
        }

        // 3. Vérifier déjà inscrit
        if ($this->inscriptionModel->dejaInscrit($eleve_id, $anneeActive['id'])) {
            $_SESSION['error'] = 'Cet élève est déjà inscrit pour l\'année ' . $anneeActive['libelle'];
            header('Location: /gestionecole/inscription/reinscription');
            exit;
        }

        // 4. Récupérer montant scolarité
        $montantScolarite = $this->inscriptionModel->getMontantScolarite(
            $classe_id,
            $anneeActive['id'],
            $currentUser['ecole_id']
        );

        // 5. Stocker en session
        $_SESSION['inscription_data'] = [
            'type'              => 'reinscription',
            'ecole_id'          => $currentUser['ecole_id'],
            'eleve_id'          => $eleve_id,
            'classe_id'         => $classe_id,
            'annee_id'          => $anneeActive['id'],
            'annee_libelle'     => $anneeActive['libelle'],
            'montant_scolarite' => $montantScolarite,
            'eleve' => [
                'nom'       => $eleve['nom'],
                'prenom'    => $eleve['prenom'],
                'matricule' => $eleve['matricule'],
            ]
        ];

        header('Location: /gestionecole/inscription/paiement');
        exit;
    }

    // ─────────────────────────────────────────
    // ÉTAPE 2 — PAIEMENT
    // GET  /gestionecole/inscription/paiement
    // POST /gestionecole/inscription/paiement
    // ─────────────────────────────────────────
    public function paiement() {
        if (!isset($_SESSION['inscription_data'])) {
            header('Location: /gestionecole/inscription/choix');
            exit;
        }

        $data   = $_SESSION['inscription_data'];
        $classe = $this->classeModel->findById($data['classe_id']);

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            require_once 'views/inscriptions/paiement.php';
            return;
        }

        $montant       = (float)($_POST['montant'] ?? 0);
        $statut        = trim($_POST['statut'] ?? '');
        $date_paiement = trim($_POST['date_paiement'] ?? date('Y-m-d H:i:s'));

        // 1. Validation
        if ($montant <= 0) {
            $_SESSION['error'] = 'Le montant doit être supérieur à 0.';
            header('Location: /gestionecole/inscription/paiement');
            exit;
        }

        if (!in_array($statut, ['paye', 'partiel', 'impaye'])) {
            $_SESSION['error'] = 'Statut de paiement invalide.';
            header('Location: /gestionecole/inscription/paiement');
            exit;
        }

        // 2. Début transaction
        $this->db->beginTransaction();

        try {
            $ecole_id  = $data['ecole_id'];
            $classe_id = $data['classe_id'];
            $annee_id  = $data['annee_id'];

            // 3a. Si nouvelle inscription → créer l'élève
            if ($data['type'] === 'inscription') {
                $e     = $data['eleve'];
                $eleve = $this->eleveModel->create(
                    $ecole_id,
                    $e['matricule'], $e['nom'], $e['prenom'],
                    $e['date_naissance'], $e['lieu_naissance'],
                    $e['domicile'], $e['tel_tuteur'], $e['nom_tuteur'],
                    $e['nom_pere'], $e['nom_mere'],
                    $e['tel_pere'], $e['tel_mere'],
                    $e['profession_pere']
                );
                $eleve_id = $eleve['id'];
            } else {
                $eleve_id = $data['eleve_id'];
            }

            // 3b. Créer l'inscription
            $inscription = $this->inscriptionModel->create(
                $ecole_id, $eleve_id, $classe_id, $annee_id
            );

            // 3c. Enregistrer le paiement
            $paiement = $this->paiementModel->create(
                $ecole_id,
                $inscription['id'],
                $montant,
                $date_paiement,
                $statut
            );

            // 3d. Générer numéro facture
            $numero_facture = $this->paiementModel->genererNumeroFacture(
                $ecole_id,
                $annee_id
            );

            // 3e. Calculer reste à payer
            $montant_reste = $data['montant_scolarite'] - $montant;
            $montant_reste = $montant_reste < 0 ? 0 : $montant_reste;

            // 3f. Créer la facture
            $this->paiementModel->creerFacture(
                $ecole_id,
                $paiement['id'],
                $inscription['id'],
                $numero_facture,
                $data['eleve']['nom'] . ' ' . $data['eleve']['prenom'],
                $classe['nom'],
                $data['annee_libelle'],
                $data['montant_scolarite'],
                $montant,
                $montant_reste,
                $date_paiement
            );

            // Valider transaction
            $this->db->commit();

            // 4. Stocker pour page succès
            $_SESSION['inscription_succes'] = [
                'numero_facture'    => $numero_facture,
                'eleve_nom'         => $data['eleve']['nom'] . ' ' . $data['eleve']['prenom'],
                'classe_nom'        => $classe['nom'],
                'annee_libelle'     => $data['annee_libelle'],
                'montant_scolarite' => $data['montant_scolarite'],
                'montant_paye'      => $montant,
                'montant_reste'     => $montant_reste,
                'date_paiement'     => $date_paiement,
                'statut'            => $statut,
            ];

            unset($_SESSION['inscription_data']);

            header('Location: /gestionecole/inscription/succes');
            exit;

        } catch (Exception $e) {
            $this->db->rollBack();
            $_SESSION['error'] = 'Erreur : ' . $e->getMessage();
            header('Location: /gestionecole/inscription/paiement');
            exit;
        }
    }

    // ─────────────────────────────────────────
    // ÉTAPE 3 — SUCCÈS
    // GET /gestionecole/inscription/succes
    // ─────────────────────────────────────────
    public function succes() {
        if (!isset($_SESSION['inscription_succes'])) {
            header('Location: /gestionecole/inscription/index');
            exit;
        }

        $succes = $_SESSION['inscription_succes'];
        unset($_SESSION['inscription_succes']);

        require_once 'views/inscriptions/succes.php';
    }

    // ─────────────────────────────────────────
    // SUPPRESSION
    // GET /gestionecole/inscription/delete?id=1
    // ─────────────────────────────────────────
    public function delete() {
        Auth::role('super_admin', 'admin');
        $currentUser = Auth::user();
        $id = $_GET['id'] ?? null;

        if (!$id) {
            header('Location: /gestionecole/inscription/index');
            exit;
        }

        $inscription = $this->inscriptionModel->findById($id);

        if (!$inscription) {
            $_SESSION['error'] = 'Inscription introuvable.';
            header('Location: /gestionecole/inscription/index');
            exit;
        }

        if ($currentUser['role'] === 'admin' &&
            $inscription['ecole_id'] != $currentUser['ecole_id']) {
            $_SESSION['error'] = 'Action non autorisée.';
            header('Location: /gestionecole/inscription/index');
            exit;
        }

        $this->inscriptionModel->delete($id);

        $_SESSION['success'] = 'Inscription supprimée avec succès.';
        header('Location: /gestionecole/inscription/index');
        exit;
    }
    // ─────────────────────────────────────────
// DÉTAIL INSCRIPTION
// GET /gestionecole/inscription/detail?id=1
// ─────────────────────────────────────────
public function detail() {
    $currentUser = Auth::user();
    $id = $_GET['id'] ?? null;

    if (!$id) {
        header('Location: /gestionecole/inscription/index');
        exit;
    }

    $inscription = $this->inscriptionModel->findById($id);

    if (!$inscription) {
        $_SESSION['error'] = 'Inscription introuvable.';
        header('Location: /gestionecole/inscription/index');
        exit;
    }

    // Admin/secretaire ne peut pas voir une inscription d'une autre école
    if (in_array($currentUser['role'], ['admin', 'secretaire']) &&
        $inscription['ecole_id'] != $currentUser['ecole_id']) {
        $_SESSION['error'] = 'Action non autorisée.';
        header('Location: /gestionecole/inscription/index');
        exit;
    }

    // Récupérer les paiements de l'inscription
    $paiements = $this->paiementModel->findByInscription($id);

    // Calculer total payé et reste
    $totalPaye        = $this->paiementModel->totalPaye($id);
    $montantScolarite = $this->inscriptionModel->getMontantScolarite(
        $inscription['classe_id'],
        $inscription['annee_id'],
        $inscription['ecole_id']
    );
    $resteAPayer = $montantScolarite - $totalPaye;
    $resteAPayer = $resteAPayer < 0 ? 0 : $resteAPayer;

    require_once 'views/inscriptions/detail.php';
}
}