<?php
// controllers/NoteController.php

require_once 'models/note.php';
require_once 'models/classe.php';
require_once 'models/matiere.php';
require_once 'models/composition.php';
require_once 'models/annee.php';
require_once 'models/inscription.php';
require_once 'middleware/auth.php';

class NoteController {
    private $noteModel;
    private $classeModel;
    private $matiereModel;
    private $compositionModel;
    private $anneeModel;
    private $inscriptionModel;

    public function __construct() {
        Auth::role('super_admin', 'admin', 'enseignant', 'secretaire');
        $this->noteModel        = new Note();
        $this->classeModel      = new Classe();
        $this->matiereModel     = new Matiere();
        $this->compositionModel = new Composition();
        $this->anneeModel       = new Annee();
        $this->inscriptionModel = new Inscription();
    }

    // ─────────────────────────────────────────
    // INDEX
    // GET /gestionecole/note/index
    // ─────────────────────────────────────────
    public function index() {
        $currentUser = Auth::user();
        $anneeActive = $this->anneeModel->findActive();

        if (!$anneeActive) {
            $_SESSION['error'] = 'Aucune année scolaire active.';
            header('Location: /gestionecole/annee/index');
            exit;
        }

        if ($currentUser['role'] === 'enseignant') {
            $classes = $this->classeModel->findByEnseignant($currentUser['id']);
        } else {
            $classes = $this->classeModel->findByEcole($currentUser['ecole_id']);
        }

        $compositions = $this->compositionModel->findByAnnee(
            $currentUser['ecole_id'],
            $anneeActive['id']
        );

        require_once 'views/notes/index.php';
    }

    // ─────────────────────────────────────────
    // SAISIE DES NOTES
    // GET  /gestionecole/note/saisie?classe_id=1&composition_id=1
    // POST /gestionecole/note/saisie?classe_id=1&composition_id=1
    // ─────────────────────────────────────────
    public function saisie() {
        Auth::role('enseignant', 'admin', 'secretaire');
        $currentUser    = Auth::user();
        $classe_id      = $_GET['classe_id'] ?? null;
        $composition_id = $_GET['composition_id'] ?? null;

        if (!$classe_id || !$composition_id) {
            $_SESSION['error'] = 'Veuillez choisir une classe et une composition.';
            header('Location: /gestionecole/note/index');
            exit;
        }

        $anneeActive = $this->anneeModel->findActive();
        $classe      = $this->classeModel->findById($classe_id);
        $composition = $this->compositionModel->findById($composition_id);

        if (!$classe || !$composition) {
            $_SESSION['error'] = 'Classe ou composition introuvable.';
            header('Location: /gestionecole/note/index');
            exit;
        }

        // Vérification droits enseignant
        if ($currentUser['role'] === 'enseignant' &&
            $classe['enseignant_id'] != $currentUser['id']) {
            $_SESSION['error'] = 'Action non autorisée.';
            header('Location: /gestionecole/note/index');
            exit;
        }

        // Élèves inscrits dans la classe
        $inscriptions = $this->inscriptionModel->findByClasse(
            $classe_id,
            $anneeActive['id']
        );

        // Matières de la classe uniquement
        $matieres = $this->matiereModel->findByClasse($classe_id);

        if (empty($matieres)) {
            $_SESSION['error'] = 'Aucune matière définie pour cette classe.';
            header('Location: /gestionecole/note/index');
            exit;
        }

        // Notes existantes
        $notesExistantes = [];
        foreach ($inscriptions as $inscription) {
            foreach ($matieres as $matiere) {
                $note = $this->noteModel->findByInscriptionMatiereComposition(
                    $inscription['id'],
                    $matiere['id'],
                    $composition_id
                );
                $notesExistantes[$inscription['id']][$matiere['id']] = $note
                    ? $note['note']
                    : '';
            }
        }

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            require_once 'views/notes/saisie.php';
            return;
        }

        // Traitement POST
        $notes  = $_POST['notes'] ?? [];
        $errors = [];

        foreach ($notes as $inscription_id => $matiereNotes) {
            foreach ($matiereNotes as $matiere_id => $note) {
                $note = trim($note);
                if ($note === '') continue;

                $matiere = $this->matiereModel->findById($matiere_id);
                if (!$matiere) continue;

                if (!is_numeric($note)) {
                    $errors[] = 'Note invalide pour ' . $matiere['nom'];
                    continue;
                }

                $note = (float)$note;

                if ($note < 0 || $note > $matiere['note_sur']) {
                    $errors[] = htmlspecialchars($matiere['nom']) .
                        ' : note entre 0 et ' . $matiere['note_sur'];
                    continue;
                }

                $this->noteModel->save(
                    $currentUser['ecole_id'],
                    $inscription_id,
                    $currentUser['id'],
                    $matiere_id,
                    $composition_id,
                    $note
                );
            }
        }

        if (!empty($errors)) {
            $_SESSION['error'] = implode('<br>', $errors);
        } else {
            $_SESSION['success'] = 'Notes enregistrées avec succès.';
        }

        header('Location: /gestionecole/note/saisie?classe_id=' . $classe_id . '&composition_id=' . $composition_id);
        exit;
    }

    // ─────────────────────────────────────────
    // RÉSULTATS
    // GET /gestionecole/note/resultats?classe_id=1&composition_id=1
    // ─────────────────────────────────────────
    public function resultats() {
        Auth::role('super_admin', 'admin', 'enseignant', 'secretaire');
        $currentUser    = Auth::user();
        $classe_id      = $_GET['classe_id'] ?? null;
        $composition_id = $_GET['composition_id'] ?? null;

        if (!$classe_id || !$composition_id) {
            $_SESSION['error'] = 'Veuillez choisir une classe et une composition.';
            header('Location: /gestionecole/note/index');
            exit;
        }

        $anneeActive = $this->anneeModel->findActive();
        $classe      = $this->classeModel->findById($classe_id);
        $composition = $this->compositionModel->findById($composition_id);

        // Matières de la classe uniquement
        $matieres = $this->matiereModel->findByClasse($classe_id);

        // Rangs
        $rangs = $this->noteModel->calculerRangs($classe_id, $composition_id);

        // Notes par élève
        $notesParEleve = [];
        foreach ($rangs as $r) {
            $notes = $this->noteModel->findByInscriptionComposition(
                $r['inscription_id'],
                $composition_id
            );
            foreach ($notes as $note) {
                $notesParEleve[$r['inscription_id']][$note['matiere_id']] = $note;
            }
        }

        require_once 'views/notes/resultat.php';
    }

    // ─────────────────────────────────────────
    // BULLETIN
    // GET /gestionecole/note/bulletin?inscription_id=1&composition_id=1
    // ─────────────────────────────────────────
    public function bulletin() {
        $currentUser    = Auth::user();
        $inscription_id = $_GET['inscription_id'] ?? null;
        $composition_id = $_GET['composition_id'] ?? null;

        if (!$inscription_id || !$composition_id) {
            header('Location: /gestionecole/note/index');
            exit;
        }

        $inscription = $this->inscriptionModel->findById($inscription_id);
        $composition = $this->compositionModel->findById($composition_id);
        $anneeActive = $this->anneeModel->findActive();

        if (!$inscription || !$composition) {
            $_SESSION['error'] = 'Données introuvables.';
            header('Location: /gestionecole/note/index');
            exit;
        }

        // Matières de la classe de l'élève
        $matieres = $this->matiereModel->findByClasse($inscription['classe_id']);

        // Bulletin
        $bulletin = $this->noteModel->getBulletin($inscription_id, $composition_id);

        // Moyenne
        $moyenne = $this->noteModel->calculerMoyenne($inscription_id, $composition_id);

        // Rang
        $rangs       = $this->noteModel->calculerRangs(
            $inscription['classe_id'],
            $composition_id
        );
        $rang        = '—';
        $totalEleves = count($rangs);

        foreach ($rangs as $r) {
            if ($r['inscription_id'] == $inscription_id) {
                $rang = $r['rang'];
                break;
            }
        }

        require_once 'views/notes/bulletin.php';
    }
}