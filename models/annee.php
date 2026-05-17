<?php
// models/Annee.php

require_once 'config/Database.php';

class Annee {
    private $db;

    public function __construct() {
        $database = new Database();
        $this->db = $database->getConnection();
    }

    // ─────────────────────────────────────────
    // Lister toutes les années
    // ─────────────────────────────────────────
    public function findAll() {
        $stmt = $this->db->query(
            "SELECT * FROM annees
             ORDER BY libelle DESC"
        );
        return $stmt->fetchAll();
    }

    // ─────────────────────────────────────────
    // Trouver une année par ID
    // ─────────────────────────────────────────
    public function findById($id) {
        $stmt = $this->db->prepare(
            "SELECT * FROM annees WHERE id = ? LIMIT 1"
        );
        $stmt->execute([$id]);
        return $stmt->fetch();
    }

    // ─────────────────────────────────────────
    // Trouver l'année active
    // ─────────────────────────────────────────
    public function findActive() {
        $stmt = $this->db->query(
            "SELECT * FROM annees WHERE etat = 1 LIMIT 1"
        );
        return $stmt->fetch();
    }

    // ─────────────────────────────────────────
    // Créer une année
    // ─────────────────────────────────────────
    public function create($libelle) {
        $stmt = $this->db->prepare(
            "INSERT INTO annees (libelle, etat)
             VALUES (?, 0)"
        );
        $stmt->execute([$libelle]);
        return $this->findById($this->db->lastInsertId());
    }

    // ─────────────────────────────────────────
    // Modifier une année
    // ─────────────────────────────────────────
    public function update($id, $libelle) {
        $stmt = $this->db->prepare(
            "UPDATE annees SET libelle = ? WHERE id = ?"
        );
        return $stmt->execute([$libelle, $id]);
    }

    // ─────────────────────────────────────────
    // Activer une année
    // Désactive toutes les autres d'abord
    // ─────────────────────────────────────────
    public function activate($id) {
        // 1. Désactiver toutes les années
        $this->db->exec("UPDATE annees SET etat = 0");

        // 2. Activer l'année choisie
        $stmt = $this->db->prepare(
            "UPDATE annees SET etat = 1 WHERE id = ?"
        );
        return $stmt->execute([$id]);
    }

    // ─────────────────────────────────────────
    // Désactiver une année
    // ─────────────────────────────────────────
    public function deactivate($id) {
        $stmt = $this->db->prepare(
            "UPDATE annees SET etat = 0 WHERE id = ?"
        );
        return $stmt->execute([$id]);
    }

    // ─────────────────────────────────────────
    // Supprimer une année
    // ─────────────────────────────────────────
    public function delete($id) {
        $stmt = $this->db->prepare(
            "DELETE FROM annees WHERE id = ?"
        );
        return $stmt->execute([$id]);
    }

    // ─────────────────────────────────────────
    // Vérifier si libellé existe déjà
    // ─────────────────────────────────────────
    public function libelleExists($libelle, $excludeId = null) {
        if ($excludeId) {
            $stmt = $this->db->prepare(
                "SELECT COUNT(*) FROM annees
                 WHERE libelle = ? AND id != ?"
            );
            $stmt->execute([$libelle, $excludeId]);
        } else {
            $stmt = $this->db->prepare(
                "SELECT COUNT(*) FROM annees
                 WHERE libelle = ?"
            );
            $stmt->execute([$libelle]);
        }
        return $stmt->fetchColumn() > 0;
    }

    // ─────────────────────────────────────────
    // Vérifier si une année est active
    // ─────────────────────────────────────────
    public function isActive($id) {
        $stmt = $this->db->prepare(
            "SELECT etat FROM annees WHERE id = ?"
        );
        $stmt->execute([$id]);
        return $stmt->fetchColumn() == 1;
    }
}