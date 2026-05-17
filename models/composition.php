<?php
// models/Composition.php

require_once 'config/Database.php';

class Composition {
    private $db;

    public function __construct() {
        $database = new Database();
        $this->db = $database->getConnection();
    }

    // ─────────────────────────────────────────
    // Lister toutes les compositions
    // Utilisé par le super_admin
    // ─────────────────────────────────────────
    public function findAll() {
        $stmt = $this->db->query(
            "SELECT c.*, e.nom AS ecole_nom, a.libelle AS annee_libelle
             FROM compositions c
             JOIN ecoles e ON c.ecole_id = e.id
             JOIN annees a ON c.annee_id = a.id
             ORDER BY e.nom ASC, a.libelle DESC, c.ordre ASC"
        );
        return $stmt->fetchAll();
    }

    // ─────────────────────────────────────────
    // Lister les compositions d'une école
    // pour une année donnée
    // ─────────────────────────────────────────
    public function findByAnnee($ecoleId, $anneeId) {
        $stmt = $this->db->prepare(
            "SELECT c.*, e.nom AS ecole_nom, a.libelle AS annee_libelle
             FROM compositions c
             JOIN ecoles e ON c.ecole_id = e.id
             JOIN annees a ON c.annee_id = a.id
             WHERE c.ecole_id = ? AND c.annee_id = ?
             ORDER BY c.ordre ASC"
        );
        $stmt->execute([$ecoleId, $anneeId]);
        return $stmt->fetchAll();
    }

    // ─────────────────────────────────────────
    // Trouver une composition par ID
    // ─────────────────────────────────────────
    public function findById($id) {
        $stmt = $this->db->prepare(
            "SELECT c.*, e.nom AS ecole_nom, a.libelle AS annee_libelle
             FROM compositions c
             JOIN ecoles e ON c.ecole_id = e.id
             JOIN annees a ON c.annee_id = a.id
             WHERE c.id = ?
             LIMIT 1"
        );
        $stmt->execute([$id]);
        return $stmt->fetch();
    }

    // ─────────────────────────────────────────
    // Créer une composition
    // ─────────────────────────────────────────
    public function create($ecole_id, $annee_id, $libelle, $ordre, $date_debut, $date_fin) {
        $stmt = $this->db->prepare(
            "INSERT INTO compositions (ecole_id, annee_id, libelle, ordre, date_debut, date_fin)
             VALUES (?, ?, ?, ?, ?, ?)"
        );
        $stmt->execute([$ecole_id, $annee_id, $libelle, $ordre, $date_debut, $date_fin]);
        return $this->findById($this->db->lastInsertId());
    }

    // ─────────────────────────────────────────
    // Modifier une composition
    // ─────────────────────────────────────────
    public function update($id, $libelle, $ordre, $date_debut, $date_fin) {
        $stmt = $this->db->prepare(
            "UPDATE compositions
             SET libelle = ?, ordre = ?, date_debut = ?, date_fin = ?
             WHERE id = ?"
        );
        return $stmt->execute([$libelle, $ordre, $date_debut, $date_fin, $id]);
    }

    // ─────────────────────────────────────────
    // Supprimer une composition
    // ─────────────────────────────────────────
    public function delete($id) {
        $stmt = $this->db->prepare(
            "DELETE FROM compositions WHERE id = ?"
        );
        return $stmt->execute([$id]);
    }

    // ─────────────────────────────────────────
    // Compter les compositions d'une école
    // pour une année donnée
    // ─────────────────────────────────────────
    public function countByAnnee($ecoleId, $anneeId) {
        $stmt = $this->db->prepare(
            "SELECT COUNT(*) FROM compositions
             WHERE ecole_id = ? AND annee_id = ?"
        );
        $stmt->execute([$ecoleId, $anneeId]);
        return $stmt->fetchColumn();
    }

    // ─────────────────────────────────────────
    // Vérifier si libellé existe déjà
    // pour une école et une année
    // ─────────────────────────────────────────
    public function libelleExists($ecoleId, $anneeId, $libelle, $excludeId = null) {
        if ($excludeId) {
            $stmt = $this->db->prepare(
                "SELECT COUNT(*) FROM compositions
                 WHERE ecole_id = ? AND annee_id = ?
                 AND libelle = ? AND id != ?"
            );
            $stmt->execute([$ecoleId, $anneeId, $libelle, $excludeId]);
        } else {
            $stmt = $this->db->prepare(
                "SELECT COUNT(*) FROM compositions
                 WHERE ecole_id = ? AND annee_id = ?
                 AND libelle = ?"
            );
            $stmt->execute([$ecoleId, $anneeId, $libelle]);
        }
        return $stmt->fetchColumn() > 0;
    }

    // ─────────────────────────────────────────
    // Vérifier si ordre existe déjà
    // pour une école et une année
    // ─────────────────────────────────────────
    public function ordreExists($ecoleId, $anneeId, $ordre, $excludeId = null) {
        if ($excludeId) {
            $stmt = $this->db->prepare(
                "SELECT COUNT(*) FROM compositions
                 WHERE ecole_id = ? AND annee_id = ?
                 AND ordre = ? AND id != ?"
            );
            $stmt->execute([$ecoleId, $anneeId, $ordre, $excludeId]);
        } else {
            $stmt = $this->db->prepare(
                "SELECT COUNT(*) FROM compositions
                 WHERE ecole_id = ? AND annee_id = ?
                 AND ordre = ?"
            );
            $stmt->execute([$ecoleId, $anneeId, $ordre]);
        }
        return $stmt->fetchColumn() > 0;
    }

    // ─────────────────────────────────────────
    // Récupérer le prochain ordre disponible
    // ─────────────────────────────────────────
    public function getNextOrdre($ecoleId, $anneeId) {
        $stmt = $this->db->prepare(
            "SELECT COALESCE(MAX(ordre), 0) + 1
             FROM compositions
             WHERE ecole_id = ? AND annee_id = ?"
        );
        $stmt->execute([$ecoleId, $anneeId]);
        return $stmt->fetchColumn();
    }

    // ─────────────────────────────────────────
    // Vérifier chevauchement de dates
    // ─────────────────────────────────────────
    public function datesChevauchent($ecoleId, $anneeId, $date_debut, $date_fin, $excludeId = null) {
        if ($excludeId) {
            $stmt = $this->db->prepare(
                "SELECT COUNT(*) FROM compositions
                 WHERE ecole_id = ? AND annee_id = ?
                 AND id != ?
                 AND (
                     (date_debut <= ? AND date_fin >= ?)
                     OR (date_debut <= ? AND date_fin >= ?)
                     OR (date_debut >= ? AND date_fin <= ?)
                 )"
            );
            $stmt->execute([
                $ecoleId, $anneeId, $excludeId,
                $date_fin, $date_debut,
                $date_debut, $date_fin,
                $date_debut, $date_fin
            ]);
        } else {
            $stmt = $this->db->prepare(
                "SELECT COUNT(*) FROM compositions
                 WHERE ecole_id = ? AND annee_id = ?
                 AND (
                     (date_debut <= ? AND date_fin >= ?)
                     OR (date_debut <= ? AND date_fin >= ?)
                     OR (date_debut >= ? AND date_fin <= ?)
                 )"
            );
            $stmt->execute([
                $ecoleId, $anneeId,
                $date_fin, $date_debut,
                $date_debut, $date_fin,
                $date_debut, $date_fin
            ]);
        }
        return $stmt->fetchColumn() > 0;
    }
}