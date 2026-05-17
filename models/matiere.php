<?php
// models/Matiere.php

require_once 'config/Database.php';

class Matiere {
    private $db;

    public function __construct() {
        $database = new Database();
        $this->db = $database->getConnection();
    }

    // ─────────────────────────────────────────
    // Lister toutes les matières
    // ─────────────────────────────────────────
    public function findAll() {
        $stmt = $this->db->query(
            "SELECT m.*, e.nom AS ecole_nom, c.nom AS classe_nom
             FROM matieres m
             LEFT JOIN ecoles e ON m.ecole_id = e.id
             LEFT JOIN classes c ON m.classe_id = c.id
             ORDER BY e.nom ASC, c.nom ASC, m.nom ASC"
        );
        return $stmt->fetchAll();
    }

    // ─────────────────────────────────────────
    // Lister les matières d'une école
    // ─────────────────────────────────────────
    public function findByEcole($ecoleId) {
        $stmt = $this->db->prepare(
            "SELECT m.*, c.nom AS classe_nom
             FROM matieres m
             LEFT JOIN classes c ON m.classe_id = c.id
             WHERE m.ecole_id = ?
             ORDER BY c.nom ASC, m.nom ASC"
        );
        $stmt->execute([$ecoleId]);
        return $stmt->fetchAll();
    }

    // ─────────────────────────────────────────
    // Lister les matières d'une classe
    // ─────────────────────────────────────────
    public function findByClasse($classeId) {
        $stmt = $this->db->prepare(
            "SELECT m.*, c.nom AS classe_nom
             FROM matieres m
             LEFT JOIN classes c ON m.classe_id = c.id
             WHERE m.classe_id = ?
             ORDER BY m.nom ASC"
        );
        $stmt->execute([$classeId]);
        return $stmt->fetchAll();
    }

    // ─────────────────────────────────────────
    // Trouver une matière par ID
    // ─────────────────────────────────────────
    public function findById($id) {
        $stmt = $this->db->prepare(
            "SELECT m.*, e.nom AS ecole_nom, c.nom AS classe_nom
             FROM matieres m
             LEFT JOIN ecoles e ON m.ecole_id = e.id
             LEFT JOIN classes c ON m.classe_id = c.id
             WHERE m.id = ?
             LIMIT 1"
        );
        $stmt->execute([$id]);
        return $stmt->fetch();
    }

    // ─────────────────────────────────────────
    // Créer une matière
    // ─────────────────────────────────────────
    
    public function create($ecole_id, $classe_id, $nom, $note_sur = 10) {
    $stmt = $this->db->prepare(
        "INSERT INTO matieres (ecole_id, classe_id, nom, note_sur)
         VALUES (?, ?, ?, ?)"
    );
    $stmt->execute([$ecole_id, $classe_id, $nom, $note_sur]);
    return $this->findById($this->db->lastInsertId());
}

// Modifier update()
public function update($id, $nom, $note_sur) {
    $stmt = $this->db->prepare(
        "UPDATE matieres SET nom = ?, note_sur = ? WHERE id = ?"
    );
    return $stmt->execute([$nom, $note_sur, $id]);
}

    // ─────────────────────────────────────────
    // Supprimer une matière
    // ─────────────────────────────────────────
    public function delete($id) {
        $stmt = $this->db->prepare(
            "DELETE FROM matieres WHERE id = ?"
        );
        return $stmt->execute([$id]);
    }

    // ─────────────────────────────────────────
    // Vérifier si matière existe déjà
    // dans une classe
    // ─────────────────────────────────────────
    public function matiereExists($classeId, $nom, $excludeId = null) {
        if ($excludeId) {
            $stmt = $this->db->prepare(
                "SELECT COUNT(*) FROM matieres
                 WHERE classe_id = ? AND nom = ? AND id != ?"
            );
            $stmt->execute([$classeId, $nom, $excludeId]);
        } else {
            $stmt = $this->db->prepare(
                "SELECT COUNT(*) FROM matieres
                 WHERE classe_id = ? AND nom = ?"
            );
            $stmt->execute([$classeId, $nom]);
        }
        return $stmt->fetchColumn() > 0;
    }

    // ─────────────────────────────────────────
    // Compter les matières d'une classe
    // ─────────────────────────────────────────
    public function countByClasse($classeId) {
        $stmt = $this->db->prepare(
            "SELECT COUNT(*) FROM matieres WHERE classe_id = ?"
        );
        $stmt->execute([$classeId]);
        return $stmt->fetchColumn();
    }

    // ─────────────────────────────────────────
    // Compter les matières d'une école
    // ─────────────────────────────────────────
    public function countByEcole($ecoleId) {
        $stmt = $this->db->prepare(
            "SELECT COUNT(*) FROM matieres WHERE ecole_id = ?"
        );
        $stmt->execute([$ecoleId]);
        return $stmt->fetchColumn();
    }
}