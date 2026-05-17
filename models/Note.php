<?php
// models/Note.php

require_once 'config/Database.php';

class Note {
    private $db;

    public function __construct() {
        $database = new Database();
        $this->db = $database->getConnection();
    }

    // ─────────────────────────────────────────
    // Lister les notes d'une classe
    // pour une composition donnée
    // ─────────────────────────────────────────
    public function findByClasseComposition($classeId, $compositionId) {
        $stmt = $this->db->prepare(
            "SELECT n.*,
                    e.matricule, e.nom AS eleve_nom, e.prenom AS eleve_prenom,
                    m.nom AS matiere_nom, m.coefficient, m.note_sur,
                    i.id AS inscription_id
             FROM notes n
             JOIN inscriptions i ON n.inscription_id = i.id
             JOIN eleves e ON i.eleve_id = e.id
             JOIN matieres m ON n.matiere_id = m.id
             WHERE i.classe_id = ? AND n.composition_id = ?
             ORDER BY e.nom ASC, m.nom ASC"
        );
        $stmt->execute([$classeId, $compositionId]);
        return $stmt->fetchAll();
    }

    // ─────────────────────────────────────────
    // Lister les notes d'un élève
    // pour une composition donnée
    // ─────────────────────────────────────────
    public function findByInscriptionComposition($inscriptionId, $compositionId) {
        $stmt = $this->db->prepare(
            "SELECT n.*,
                    m.nom AS matiere_nom, m.note_sur
             FROM notes n
             JOIN matieres m ON n.matiere_id = m.id
             WHERE n.inscription_id = ? AND n.composition_id = ?
             ORDER BY m.nom ASC"
        );
        $stmt->execute([$inscriptionId, $compositionId]);
        return $stmt->fetchAll();
    }

    // ─────────────────────────────────────────
    // Lister toutes les notes d'un élève
    // toutes compositions confondues
    // ─────────────────────────────────────────
    public function findByInscription($inscriptionId) {
        $stmt = $this->db->prepare(
            "SELECT n.*,
                    m.nom AS matiere_nom, m.coefficient, m.note_sur,
                    c.libelle AS composition_libelle, c.ordre AS composition_ordre
             FROM notes n
             JOIN matieres m ON n.matiere_id = m.id
             JOIN compositions c ON n.composition_id = c.id
             WHERE n.inscription_id = ?
             ORDER BY c.ordre ASC, m.nom ASC"
        );
        $stmt->execute([$inscriptionId]);
        return $stmt->fetchAll();
    }

    // ─────────────────────────────────────────
    // Trouver une note par ID
    // ─────────────────────────────────────────
    public function findById($id) {
        $stmt = $this->db->prepare(
            "SELECT n.*,
                    m.nom AS matiere_nom, m.coefficient, m.note_sur,
                    c.libelle AS composition_libelle
             FROM notes n
             JOIN matieres m ON n.matiere_id = m.id
             JOIN compositions c ON n.composition_id = c.id
             WHERE n.id = ?
             LIMIT 1"
        );
        $stmt->execute([$id]);
        return $stmt->fetch();
    }

    // ─────────────────────────────────────────
    // Trouver une note par inscription + matière + composition
    // ─────────────────────────────────────────
    public function findByInscriptionMatiereComposition($inscriptionId, $matiereId, $compositionId) {
        $stmt = $this->db->prepare(
            "SELECT * FROM notes
             WHERE inscription_id = ? AND matiere_id = ? AND composition_id = ?
             LIMIT 1"
        );
        $stmt->execute([$inscriptionId, $matiereId, $compositionId]);
        return $stmt->fetch();
    }

    // ─────────────────────────────────────────
    // Créer ou mettre à jour une note
    // ─────────────────────────────────────────
    public function save($ecole_id, $inscription_id, $enseignant_id, $matiere_id, $composition_id, $note) {
        // Vérifier si la note existe déjà
        $existing = $this->findByInscriptionMatiereComposition(
            $inscription_id, $matiere_id, $composition_id
        );

        if ($existing) {
            // Mettre à jour
            $stmt = $this->db->prepare(
                "UPDATE notes SET note = ? WHERE id = ?"
            );
            return $stmt->execute([$note, $existing['id']]);
        } else {
            // Créer
            $stmt = $this->db->prepare(
                "INSERT INTO notes (ecole_id, inscription_id, enseignant_id, matiere_id, composition_id, note)
                 VALUES (?, ?, ?, ?, ?, ?)"
            );
            return $stmt->execute([
                $ecole_id, $inscription_id, $enseignant_id,
                $matiere_id, $composition_id, $note
            ]);
        }
    }

    // ─────────────────────────────────────────
    // Supprimer une note
    // ─────────────────────────────────────────
    public function delete($id) {
        $stmt = $this->db->prepare("DELETE FROM notes WHERE id = ?");
        return $stmt->execute([$id]);
    }

    // ─────────────────────────────────────────
    // Calculer la moyenne d'un élève
    // pour une composition donnée
    // Formule : Somme(note × coef × 20/note_sur) / Somme(coef)
    // ─────────────────────────────────────────
    // calculerMoyenne() — moyenne simple sur 20
public function calculerMoyenne($inscriptionId, $compositionId) {
    $stmt = $this->db->prepare(
        "SELECT
            AVG(n.note * (20 / m.note_sur)) AS moyenne
         FROM notes n
         JOIN matieres m ON n.matiere_id = m.id
         WHERE n.inscription_id = ? AND n.composition_id = ?"
    );
    $stmt->execute([$inscriptionId, $compositionId]);
    $result = $stmt->fetch();
    return $result && $result['moyenne'] !== null
        ? round($result['moyenne'], 2)
        : null;
}

// calculerRangs() — sans coefficient
public function calculerRangs($classeId, $compositionId) {
    $stmt = $this->db->prepare(
        "SELECT
            i.id AS inscription_id,
            e.id AS eleve_id,
            e.matricule,
            CONCAT(e.nom, ' ', e.prenom) AS nom_eleve,
            AVG(n.note * (20 / m.note_sur)) AS moyenne
         FROM inscriptions i
         JOIN eleves e ON i.eleve_id = e.id
         LEFT JOIN notes n ON n.inscription_id = i.id
            AND n.composition_id = ?
         LEFT JOIN matieres m ON n.matiere_id = m.id
         WHERE i.classe_id = ?
         GROUP BY i.id, e.id, e.matricule, e.nom, e.prenom
         ORDER BY moyenne DESC"
    );
    $stmt->execute([$compositionId, $classeId]);
    $resultats = $stmt->fetchAll();

    $rang = 1;
    foreach ($resultats as &$r) {
        $r['moyenne'] = $r['moyenne'] ? round($r['moyenne'], 2) : null;
        $r['rang']    = $r['moyenne'] !== null ? $rang++ : '—';
    }

    return $resultats;
}

// calculerMoyenneGenerale() — sans coefficient
public function calculerMoyenneGenerale($inscriptionId) {
    $stmt = $this->db->prepare(
        "SELECT
            AVG(n.note * (20 / m.note_sur)) AS moyenne
         FROM notes n
         JOIN matieres m ON n.matiere_id = m.id
         WHERE n.inscription_id = ?"
    );
    $stmt->execute([$inscriptionId]);
    $result = $stmt->fetch();
    return $result && $result['moyenne'] !== null
        ? round($result['moyenne'], 2)
        : null;
}
    // ─────────────────────────────────────────
    // Calculer les rangs d'une classe
    // pour une composition donnée
    // ─────────────────────────────────────────
    // public function calculerRangs($classeId, $compositionId) {
    //     $stmt = $this->db->prepare(
    //         "SELECT
    //             i.id AS inscription_id,
    //             e.id AS eleve_id,
    //             e.matricule,
    //             CONCAT(e.nom, ' ', e.prenom) AS nom_eleve,
    //             SUM(n.note * m.coefficient * (20 / m.note_sur)) / SUM(m.coefficient) AS moyenne
    //          FROM inscriptions i
    //          JOIN eleves e ON i.eleve_id = e.id
    //          LEFT JOIN notes n ON n.inscription_id = i.id AND n.composition_id = ?
    //          LEFT JOIN matieres m ON n.matiere_id = m.id
    //          WHERE i.classe_id = ?
    //          GROUP BY i.id, e.id, e.matricule, e.nom, e.prenom
    //          ORDER BY moyenne DESC"
    //     );
    //     $stmt->execute([$compositionId, $classeId]);
    //     $resultats = $stmt->fetchAll();

    //     // Ajouter le rang
    //     $rang = 1;
    //     foreach ($resultats as &$r) {
    //         $r['moyenne'] = $r['moyenne'] ? round($r['moyenne'], 2) : null;
    //         $r['rang']    = $r['moyenne'] !== null ? $rang++ : '—';
    //     }

    //     return $resultats;
    // }

    // ─────────────────────────────────────────
    // Récupérer le bulletin d'un élève
    // pour une composition donnée
    // ─────────────────────────────────────────
    public function getBulletin($inscriptionId, $compositionId) {
        $stmt = $this->db->prepare(
            "SELECT
                m.nom AS matiere_nom,
                
                m.note_sur,
                n.note,
                ROUND(n.note * (20 / m.note_sur), 2) AS note_sur_20,
                ROUND(n.note  * (20 / m.note_sur), 2) AS note_ponderee,
                -- Moyenne de la classe pour cette matière
                ROUND(AVG(n2.note * (20 / m.note_sur)), 2) AS moyenne_classe
             FROM matieres m
             LEFT JOIN notes n ON n.matiere_id = m.id
                AND n.inscription_id = ?
                AND n.composition_id = ?
             LEFT JOIN inscriptions i2 ON i2.classe_id = (
                SELECT classe_id FROM inscriptions WHERE id = ?
             ) AND i2.annee_id = (
                SELECT annee_id FROM inscriptions WHERE id = ?
             )
             LEFT JOIN notes n2 ON n2.matiere_id = m.id
                AND n2.inscription_id = i2.id
                AND n2.composition_id = ?
             WHERE m.ecole_id = (
                SELECT ecole_id FROM inscriptions WHERE id = ?
             )
             
             ORDER BY m.nom ASC"
        );
        $stmt->execute([
            $inscriptionId, $compositionId,
            $inscriptionId, $inscriptionId,
            $compositionId, $inscriptionId
        ]);
        return $stmt->fetchAll();
    }

    // ─────────────────────────────────────────
    // Vérifier si toutes les notes sont saisies
    // pour un élève et une composition
    // ─────────────────────────────────────────
    public function notesCompletes($inscriptionId, $compositionId, $ecoleId) {
        // Nombre de matières de l'école
        $stmt = $this->db->prepare(
            "SELECT COUNT(*) FROM matieres WHERE ecole_id = ?"
        );
        $stmt->execute([$ecoleId]);
        $nbMatieres = $stmt->fetchColumn();

        // Nombre de notes saisies
        $stmt = $this->db->prepare(
            "SELECT COUNT(*) FROM notes
             WHERE inscription_id = ? AND composition_id = ?"
        );
        $stmt->execute([$inscriptionId, $compositionId]);
        $nbNotes = $stmt->fetchColumn();

        return $nbNotes >= $nbMatieres;
    }
}