<?php
// models/Classe.php

require_once 'config/Database.php';

class Classe {
    private $db;

    public function __construct() {
        $database = new Database();
        $this->db = $database->getConnection();
    }

    // ─────────────────────────────────────────
    // Lister toutes les classes
    // Utilisé par le super_admin
    // ─────────────────────────────────────────
    public function findAll() {
        $stmt = $this->db->query(
            "SELECT c.*,
                    e.nom AS ecole_nom,
                    CONCAT(en.nom, ' ', COALESCE(en.prenom, '')) AS enseignant_nom
             FROM classes c
             LEFT JOIN ecoles e ON c.ecole_id = e.id
             LEFT JOIN enseignants en ON c.enseignant_id = en.id
             ORDER BY e.nom ASC, c.nom ASC"
        );
        return $stmt->fetchAll();
    }

    // ─────────────────────────────────────────
    // Lister les classes d'une école
    // Utilisé par admin, secretaire
    // ─────────────────────────────────────────
    public function findByEcole($ecoleId) {
        $stmt = $this->db->prepare(
            "SELECT c.*,
                    e.nom AS ecole_nom,
                    CONCAT(en.nom, ' ', COALESCE(en.prenom, '')) AS enseignant_nom
             FROM classes c
             LEFT JOIN ecoles e ON c.ecole_id = e.id
             LEFT JOIN enseignants en ON c.enseignant_id = en.id
             WHERE c.ecole_id = ?
             ORDER BY c.nom ASC"
        );
        $stmt->execute([$ecoleId]);
        return $stmt->fetchAll();
    }

    // ─────────────────────────────────────────
    // Trouver une classe par ID
    // ─────────────────────────────────────────
    public function findById($id) {
        $stmt = $this->db->prepare(
            "SELECT c.*,
                    e.nom AS ecole_nom,
                    CONCAT(en.nom, ' ', COALESCE(en.prenom, '')) AS enseignant_nom
             FROM classes c
             LEFT JOIN ecoles e ON c.ecole_id = e.id
             LEFT JOIN enseignants en ON c.enseignant_id = en.id
             WHERE c.id = ?
             LIMIT 1"
        );
        $stmt->execute([$id]);
        return $stmt->fetch();
    }

    // ─────────────────────────────────────────
    // Lister les classes d'un enseignant
    // Utilisé par l'enseignant
    // ─────────────────────────────────────────
    public function findByEnseignant($enseignantId) {
        $stmt = $this->db->prepare(
            "SELECT c.*,
                    e.nom AS ecole_nom,
                    CONCAT(en.nom, ' ', COALESCE(en.prenom, '')) AS enseignant_nom
             FROM classes c
             LEFT JOIN ecoles e ON c.ecole_id = e.id
             LEFT JOIN enseignants en ON c.enseignant_id = en.id
             WHERE c.enseignant_id = ?
             ORDER BY c.nom ASC"
        );
        $stmt->execute([$enseignantId]);
        return $stmt->fetchAll();
    }

    // ─────────────────────────────────────────
    // Créer une classe
    // ─────────────────────────────────────────
    public function create($ecole_id, $nom, $enseignant_id) {
        $stmt = $this->db->prepare(
            "INSERT INTO classes (ecole_id, nom, enseignant_id)
             VALUES (?, ?, ?)"
        );
        $stmt->execute([$ecole_id, $nom, $enseignant_id ?? null]);
        return $this->findById($this->db->lastInsertId());
    }

    // ─────────────────────────────────────────
    // Modifier une classe
    // ─────────────────────────────────────────
    public function update($id, $nom, $enseignant_id) {
        $stmt = $this->db->prepare(
            "UPDATE classes
             SET nom = ?, enseignant_id = ?
             WHERE id = ?"
        );
        return $stmt->execute([$nom, $enseignant_id ?? null, $id]);
    }

    // ─────────────────────────────────────────
    // Supprimer une classe
    // ─────────────────────────────────────────
    public function delete($id) {
        $stmt = $this->db->prepare(
            "DELETE FROM classes WHERE id = ?"
        );
        return $stmt->execute([$id]);
    }

    // ─────────────────────────────────────────
    // Vérifier si une classe existe déjà
    // dans une école (éviter les doublons)
    // ─────────────────────────────────────────
    public function classeExists($ecoleId, $nom, $excludeId = null) {
        if ($excludeId) {
            $stmt = $this->db->prepare(
                "SELECT COUNT(*) FROM classes
                 WHERE ecole_id = ? AND nom = ? AND id != ?"
            );
            $stmt->execute([$ecoleId, $nom, $excludeId]);
        } else {
            $stmt = $this->db->prepare(
                "SELECT COUNT(*) FROM classes
                 WHERE ecole_id = ? AND nom = ?"
            );
            $stmt->execute([$ecoleId, $nom]);
        }
        return $stmt->fetchColumn() > 0;
    }

    // ─────────────────────────────────────────
    // Compter les classes d'une école
    // Utilisé dans le dashboard
    // ─────────────────────────────────────────
    public function countByEcole($ecoleId) {
        $stmt = $this->db->prepare(
            "SELECT COUNT(*) FROM classes WHERE ecole_id = ?"
        );
        $stmt->execute([$ecoleId]);
        return $stmt->fetchColumn();
    }
}