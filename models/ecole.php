<?php
// models/Ecole.php

require_once 'config/Database.php';

class Ecole {
    private $db;

    public function __construct() {
        $database = new Database();
        $this->db = $database->getConnection();
    }

    // ─────────────────────────────────────────
    // Lister toutes les écoles
    // ─────────────────────────────────────────
    public function findAll() {
        $stmt = $this->db->query(
            "SELECT * FROM ecoles ORDER BY date_creation DESC"
        );
        return $stmt->fetchAll();
    }

    // ─────────────────────────────────────────
    // Lister les écoles avec stats
    // Utilisé par le super_admin dashboard
    // ─────────────────────────────────────────
    public function findAllWithStats() {
        $stmt = $this->db->query(
            "SELECT e.*,
                    COUNT(DISTINCT u.id) AS total_admins,
                    COUNT(DISTINCT en.id) AS total_enseignants,
                    COUNT(DISTINCT el.id) AS total_eleves,
                    COUNT(DISTINCT c.id) AS total_classes
             FROM ecoles e
             LEFT JOIN users u ON u.ecole_id = e.id AND u.role = 'admin'
             LEFT JOIN enseignants en ON en.ecole_id = e.id AND en.role = 'enseignant'
             LEFT JOIN eleves el ON el.ecole_id = e.id
             LEFT JOIN classes c ON c.ecole_id = e.id
             GROUP BY e.id
             ORDER BY e.date_creation DESC"
        );
        return $stmt->fetchAll();
    }

    // ─────────────────────────────────────────
    // Trouver une école par ID
    // ─────────────────────────────────────────
    public function findById($id) {
        $stmt = $this->db->prepare(
            "SELECT * FROM ecoles WHERE id = ? LIMIT 1"
        );
        $stmt->execute([$id]);
        return $stmt->fetch();
    }

    // ─────────────────────────────────────────
    // Créer une école
    // ─────────────────────────────────────────
    public function create($nom, $adresse, $telephone, $email) {
        $stmt = $this->db->prepare(
            "INSERT INTO ecoles (nom, adresse, telephone, email)
             VALUES (?, ?, ?, ?)"
        );
        $stmt->execute([$nom, $adresse, $telephone, $email]);
        return $this->findById($this->db->lastInsertId());
    }

    // ─────────────────────────────────────────
    // Modifier une école
    // ─────────────────────────────────────────
    public function update($id, $nom, $adresse, $telephone, $email) {
        $stmt = $this->db->prepare(
            "UPDATE ecoles
             SET nom = ?, adresse = ?, telephone = ?, email = ?
             WHERE id = ?"
        );
        return $stmt->execute([$nom, $adresse, $telephone, $email, $id]);
    }

    // ─────────────────────────────────────────
    // Supprimer une école
    // ─────────────────────────────────────────
    public function delete($id) {
        $stmt = $this->db->prepare(
            "DELETE FROM ecoles WHERE id = ?"
        );
        return $stmt->execute([$id]);
    }

    // ─────────────────────────────────────────
    // Vérifier si email existe déjà
    // ─────────────────────────────────────────
    public function emailExists($email, $excludeId = null) {
        if ($excludeId) {
            $stmt = $this->db->prepare(
                "SELECT COUNT(*) FROM ecoles WHERE email = ? AND id != ?"
            );
            $stmt->execute([$email, $excludeId]);
        } else {
            $stmt = $this->db->prepare(
                "SELECT COUNT(*) FROM ecoles WHERE email = ?"
            );
            $stmt->execute([$email]);
        }
        return $stmt->fetchColumn() > 0;
    }

    // ─────────────────────────────────────────
    // Compter toutes les écoles
    // ─────────────────────────────────────────
    public function count() {
        return $this->db->query(
            "SELECT COUNT(*) FROM ecoles"
        )->fetchColumn();
    }

    // ─────────────────────────────────────────
    // Compter les élèves d'une école
    // ─────────────────────────────────────────
    public function countEleves($ecoleId) {
        $stmt = $this->db->prepare(
            "SELECT COUNT(*) FROM eleves WHERE ecole_id = ?"
        );
        $stmt->execute([$ecoleId]);
        return $stmt->fetchColumn();
    }

    // ─────────────────────────────────────────
    // Compter les enseignants d'une école
    // ─────────────────────────────────────────
    public function countEnseignants($ecoleId) {
        $stmt = $this->db->prepare(
            "SELECT COUNT(*) FROM enseignants
             WHERE ecole_id = ? AND role = 'enseignant'"
        );
        $stmt->execute([$ecoleId]);
        return $stmt->fetchColumn();
    }
}