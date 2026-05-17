<?php
// models/Eleve.php

require_once 'config/Database.php';

class Eleve {
    private $db;

    public function __construct() {
        $database = new Database();
        $this->db = $database->getConnection();
    }

    // ─────────────────────────────────────────
    // Lister tous les élèves
    // Utilisé par le super_admin
    // ─────────────────────────────────────────
    public function findAll() {
        $stmt = $this->db->query(
            "SELECT e.*, ec.nom AS ecole_nom
             FROM eleves e
             LEFT JOIN ecoles ec ON e.ecole_id = ec.id
             ORDER BY ec.nom ASC, e.nom ASC"
        );
        return $stmt->fetchAll();
    }

    // ─────────────────────────────────────────
    // Lister les élèves d'une école
    // Utilisé par admin, secretaire
    // ─────────────────────────────────────────
    public function findByEcole($ecoleId) {
        $stmt = $this->db->prepare(
            "SELECT e.*, ec.nom AS ecole_nom
             FROM eleves e
             LEFT JOIN ecoles ec ON e.ecole_id = ec.id
             WHERE e.ecole_id = ?
             ORDER BY e.nom ASC"
        );
        $stmt->execute([$ecoleId]);
        return $stmt->fetchAll();
    }

    // ─────────────────────────────────────────
    // Lister les élèves d'une classe
    // via la table inscriptions
    // Utilisé par l'enseignant
    // ─────────────────────────────────────────
    public function findByClasse($classeId) {
        $stmt = $this->db->prepare(
            "SELECT e.*, ec.nom AS ecole_nom,
                    c.nom AS classe_nom,
                    i.id AS inscription_id,
                    a.libelle AS annee_libelle
             FROM eleves e
             LEFT JOIN ecoles ec ON e.ecole_id = ec.id
             JOIN inscriptions i ON i.eleve_id = e.id
             JOIN classes c ON i.classe_id = c.id
             JOIN annees a ON i.annee_id = a.id
             WHERE i.classe_id = ?
             AND a.etat = 1
             ORDER BY e.nom ASC"
        );
        $stmt->execute([$classeId]);
        return $stmt->fetchAll();
    }

    // ─────────────────────────────────────────
    // Trouver un élève par ID
    // ─────────────────────────────────────────
    public function findById($id) {
        $stmt = $this->db->prepare(
            "SELECT e.*, ec.nom AS ecole_nom
             FROM eleves e
             LEFT JOIN ecoles ec ON e.ecole_id = ec.id
             WHERE e.id = ?
             LIMIT 1"
        );
        $stmt->execute([$id]);
        return $stmt->fetch();
    }

    // ─────────────────────────────────────────
    // Trouver un élève par matricule
    // ─────────────────────────────────────────
    public function findByMatricule($matricule) {
        $stmt = $this->db->prepare(
            "SELECT e.*, ec.nom AS ecole_nom
             FROM eleves e
             LEFT JOIN ecoles ec ON e.ecole_id = ec.id
             WHERE e.matricule = ?
             LIMIT 1"
        );
        $stmt->execute([$matricule]);
        return $stmt->fetch();
    }

    // ─────────────────────────────────────────
    // Créer un élève
    // ─────────────────────────────────────────
    public function create(
        $ecole_id, $matricule, $nom, $prenom,
        $date_naissance, $lieu_naissance, $domicile,
        $tel_tuteur, $nom_tuteur, $nom_pere, $nom_mere,
        $tel_pere, $tel_mere, $profession_pere
    ) {
        $stmt = $this->db->prepare(
            "INSERT INTO eleves (
                ecole_id, matricule, nom, prenom,
                date_naissance, lieu_naissance, domicile,
                tel_tuteur, nom_tuteur, nom_pere, nom_mere,
                tel_pere, tel_mere, profession_pere
             ) VALUES (
                ?, ?, ?, ?,
                ?, ?, ?,
                ?, ?, ?, ?,
                ?, ?, ?
             )"
        );
        $stmt->execute([
            $ecole_id, $matricule, $nom, $prenom,
            $date_naissance ?: null, $lieu_naissance, $domicile,
            $tel_tuteur, $nom_tuteur, $nom_pere, $nom_mere,
            $tel_pere, $tel_mere, $profession_pere
        ]);
        return $this->findById($this->db->lastInsertId());
    }

    // ─────────────────────────────────────────
    // Modifier un élève
    // ─────────────────────────────────────────
    public function update(
        $id, $matricule, $nom, $prenom,
        $date_naissance, $lieu_naissance, $domicile,
        $tel_tuteur, $nom_tuteur, $nom_pere, $nom_mere,
        $tel_pere, $tel_mere, $profession_pere
    ) {
        $stmt = $this->db->prepare(
            "UPDATE eleves SET
                matricule = ?, nom = ?, prenom = ?,
                date_naissance = ?, lieu_naissance = ?, domicile = ?,
                tel_tuteur = ?, nom_tuteur = ?, nom_pere = ?, nom_mere = ?,
                tel_pere = ?, tel_mere = ?, profession_pere = ?
             WHERE id = ?"
        );
        return $stmt->execute([
            $matricule, $nom, $prenom,
            $date_naissance ?: null, $lieu_naissance, $domicile,
            $tel_tuteur, $nom_tuteur, $nom_pere, $nom_mere,
            $tel_pere, $tel_mere, $profession_pere,
            $id
        ]);
    }

    // ─────────────────────────────────────────
    // Supprimer un élève
    // ─────────────────────────────────────────
    public function delete($id) {
        $stmt = $this->db->prepare(
            "DELETE FROM eleves WHERE id = ?"
        );
        return $stmt->execute([$id]);
    }

    // ─────────────────────────────────────────
    // Vérifier si matricule existe déjà
    // ─────────────────────────────────────────
    public function matriculeExists($matricule, $excludeId = null) {
        if ($excludeId) {
            $stmt = $this->db->prepare(
                "SELECT COUNT(*) FROM eleves
                 WHERE matricule = ? AND id != ?"
            );
            $stmt->execute([$matricule, $excludeId]);
        } else {
            $stmt = $this->db->prepare(
                "SELECT COUNT(*) FROM eleves
                 WHERE matricule = ?"
            );
            $stmt->execute([$matricule]);
        }
        return $stmt->fetchColumn() > 0;
    }

    // ─────────────────────────────────────────
    // Compter les élèves d'une école
    // ─────────────────────────────────────────
    public function countByEcole($ecoleId) {
        $stmt = $this->db->prepare(
            "SELECT COUNT(*) FROM eleves WHERE ecole_id = ?"
        );
        $stmt->execute([$ecoleId]);
        return $stmt->fetchColumn();
    }

    // ─────────────────────────────────────────
    // Compter tous les élèves
    // ─────────────────────────────────────────
    public function count() {
        return $this->db->query(
            "SELECT COUNT(*) FROM eleves"
        )->fetchColumn();
    }
}