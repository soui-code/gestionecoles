<?php
// models/Inscription.php

require_once 'config/Database.php';

class Inscription {
    private $db;

    public function __construct() {
        $database = new Database();
        $this->db = $database->getConnection();
    }

    // ─────────────────────────────────────────
    // Lister toutes les inscriptions
    // Utilisé par le super_admin
    // ─────────────────────────────────────────
    public function findAll() {
        $stmt = $this->db->query(
            "SELECT i.*,
                    e.nom AS eleve_nom, e.prenom AS eleve_prenom,
                    e.matricule,
                    c.nom AS classe_nom,
                    a.libelle AS annee_libelle,
                    ec.nom AS ecole_nom
             FROM inscriptions i
             JOIN eleves e ON i.eleve_id = e.id
             JOIN classes c ON i.classe_id = c.id
             JOIN annees a ON i.annee_id = a.id
             JOIN ecoles ec ON i.ecole_id = ec.id
             ORDER BY i.created_at DESC"
        );
        return $stmt->fetchAll();
    }

    // ─────────────────────────────────────────
    // Lister les inscriptions d'une école
    // Utilisé par admin, secretaire
    // ─────────────────────────────────────────
    public function findByEcole($ecoleId) {
        $stmt = $this->db->prepare(
            "SELECT i.*,
                    e.nom AS eleve_nom, e.prenom AS eleve_prenom,
                    e.matricule,
                    c.nom AS classe_nom,
                    a.libelle AS annee_libelle
             FROM inscriptions i
             JOIN eleves e ON i.eleve_id = e.id
             JOIN classes c ON i.classe_id = c.id
             JOIN annees a ON i.annee_id = a.id
             WHERE i.ecole_id = ?
             ORDER BY i.created_at DESC"
        );
        $stmt->execute([$ecoleId]);
        return $stmt->fetchAll();
    }

    // ─────────────────────────────────────────
    // Lister les inscriptions par année
    // ─────────────────────────────────────────
    public function findByAnnee($ecoleId, $anneeId) {
        $stmt = $this->db->prepare(
            "SELECT i.*,
                    e.nom AS eleve_nom, e.prenom AS eleve_prenom,
                    e.matricule,
                    c.nom AS classe_nom,
                    a.libelle AS annee_libelle
             FROM inscriptions i
             JOIN eleves e ON i.eleve_id = e.id
             JOIN classes c ON i.classe_id = c.id
             JOIN annees a ON i.annee_id = a.id
             WHERE i.ecole_id = ? AND i.annee_id = ?
             ORDER BY c.nom ASC, e.nom ASC"
        );
        $stmt->execute([$ecoleId, $anneeId]);
        return $stmt->fetchAll();
    }

    // ─────────────────────────────────────────
    // Lister les inscriptions par classe
    // ─────────────────────────────────────────
    public function findByClasse($classeId, $anneeId) {
        $stmt = $this->db->prepare(
            "SELECT i.*,
                    e.nom AS eleve_nom, e.prenom AS eleve_prenom,
                    e.matricule, e.date_naissance,
                    c.nom AS classe_nom,
                    a.libelle AS annee_libelle
             FROM inscriptions i
             JOIN eleves e ON i.eleve_id = e.id
             JOIN classes c ON i.classe_id = c.id
             JOIN annees a ON i.annee_id = a.id
             WHERE i.classe_id = ? AND i.annee_id = ?
             ORDER BY e.nom ASC"
        );
        $stmt->execute([$classeId, $anneeId]);
        return $stmt->fetchAll();
    }

    // ─────────────────────────────────────────
    // Trouver une inscription par ID
    // ─────────────────────────────────────────
    public function findById($id) {
        $stmt = $this->db->prepare(
            "SELECT i.*,
                    e.nom AS eleve_nom, e.prenom AS eleve_prenom,
                    e.matricule, e.date_naissance,
                    e.nom_pere, e.nom_mere, e.tel_pere,
                    e.tel_mere, e.tel_tuteur, e.nom_tuteur,
                    e.lieu_naissance, e.domicile,
                    c.nom AS classe_nom,
                    a.libelle AS annee_libelle,
                    ec.nom AS ecole_nom
             FROM inscriptions i
             JOIN eleves e ON i.eleve_id = e.id
             JOIN classes c ON i.classe_id = c.id
             JOIN annees a ON i.annee_id = a.id
             JOIN ecoles ec ON i.ecole_id = ec.id
             WHERE i.id = ?
             LIMIT 1"
        );
        $stmt->execute([$id]);
        return $stmt->fetch();
    }

    // ─────────────────────────────────────────
    // Trouver une inscription par élève et année
    // ─────────────────────────────────────────
    public function findByEleveAndAnnee($eleveId, $anneeId) {
        $stmt = $this->db->prepare(
            "SELECT i.*,
                    c.nom AS classe_nom,
                    a.libelle AS annee_libelle
             FROM inscriptions i
             JOIN classes c ON i.classe_id = c.id
             JOIN annees a ON i.annee_id = a.id
             WHERE i.eleve_id = ? AND i.annee_id = ?
             LIMIT 1"
        );
        $stmt->execute([$eleveId, $anneeId]);
        return $stmt->fetch();
    }

    // ─────────────────────────────────────────
    // Lister toutes les inscriptions d'un élève
    // ─────────────────────────────────────────
    public function findByEleve($eleveId) {
        $stmt = $this->db->prepare(
            "SELECT i.*,
                    c.nom AS classe_nom,
                    a.libelle AS annee_libelle
             FROM inscriptions i
             JOIN classes c ON i.classe_id = c.id
             JOIN annees a ON i.annee_id = a.id
             WHERE i.eleve_id = ?
             ORDER BY a.libelle DESC"
        );
        $stmt->execute([$eleveId]);
        return $stmt->fetchAll();
    }

    // ─────────────────────────────────────────
    // Créer une inscription
    // ─────────────────────────────────────────
    public function create($ecole_id, $eleve_id, $classe_id, $annee_id) {
        $stmt = $this->db->prepare(
            "INSERT INTO inscriptions (ecole_id, eleve_id, classe_id, annee_id)
             VALUES (?, ?, ?, ?)"
        );
        $stmt->execute([$ecole_id, $eleve_id, $classe_id, $annee_id]);
        return $this->findById($this->db->lastInsertId());
    }

    // ─────────────────────────────────────────
    // Supprimer une inscription
    // ─────────────────────────────────────────
    public function delete($id) {
        $stmt = $this->db->prepare(
            "DELETE FROM inscriptions WHERE id = ?"
        );
        return $stmt->execute([$id]);
    }

    // ─────────────────────────────────────────
    // Vérifier si élève déjà inscrit cette année
    // ─────────────────────────────────────────
    public function dejaInscrit($eleveId, $anneeId) {
        $stmt = $this->db->prepare(
            "SELECT COUNT(*) FROM inscriptions
             WHERE eleve_id = ? AND annee_id = ?"
        );
        $stmt->execute([$eleveId, $anneeId]);
        return $stmt->fetchColumn() > 0;
    }

    // ─────────────────────────────────────────
    // Compter les inscriptions d'une école
    // ─────────────────────────────────────────
    public function countByEcole($ecoleId) {
        $stmt = $this->db->prepare(
            "SELECT COUNT(*) FROM inscriptions WHERE ecole_id = ?"
        );
        $stmt->execute([$ecoleId]);
        return $stmt->fetchColumn();
    }

    // ─────────────────────────────────────────
    // Compter les inscriptions par année
    // ─────────────────────────────────────────
    public function countByAnnee($ecoleId, $anneeId) {
        $stmt = $this->db->prepare(
            "SELECT COUNT(*) FROM inscriptions
             WHERE ecole_id = ? AND annee_id = ?"
        );
        $stmt->execute([$ecoleId, $anneeId]);
        return $stmt->fetchColumn();
    }

    // ─────────────────────────────────────────
    // Calculer le total payé pour une inscription
    // ─────────────────────────────────────────
    public function totalPaye($inscriptionId) {
        $stmt = $this->db->prepare(
            "SELECT COALESCE(SUM(montant), 0)
             FROM paiements
             WHERE inscription_id = ?"
        );
        $stmt->execute([$inscriptionId]);
        return $stmt->fetchColumn();
    }

    // ─────────────────────────────────────────
    // Récupérer le montant de scolarité
    // pour une inscription
    // ─────────────────────────────────────────
    public function getMontantScolarite($classeId, $anneeId, $ecoleId) {
        $stmt = $this->db->prepare(
            "SELECT montant_annuel FROM scolarites
             WHERE classe_id = ? AND annee_id = ? AND ecole_id = ?
             LIMIT 1"
        );
        $stmt->execute([$classeId, $anneeId, $ecoleId]);
        return $stmt->fetchColumn() ?: 0;
    }
}