<?php
// models/Scolarite.php

require_once 'config/Database.php';

class Scolarite {
    private $db;

    public function __construct() {
        $database = new Database();
        $this->db = $database->getConnection();
    }

    // ─────────────────────────────────────────
    // Créer ou mettre à jour une scolarité
    // ON DUPLICATE KEY UPDATE évite les doublons
    // ─────────────────────────────────────────
    public function save($ecole_id, $classe_id, $annee_id, $montant_inscription, $montant_mensuel, $montant_annuel) {
        $stmt = $this->db->prepare(
            "INSERT INTO scolarites
                (ecole_id, classe_id, annee_id, montant_inscription, montant_mensuel, montant_annuel)
             VALUES (?, ?, ?, ?, ?, ?)
             ON DUPLICATE KEY UPDATE
                montant_inscription = VALUES(montant_inscription),
                montant_mensuel     = VALUES(montant_mensuel),
                montant_annuel      = VALUES(montant_annuel)"
        );
        return $stmt->execute([
            $ecole_id, $classe_id, $annee_id,
            $montant_inscription, $montant_mensuel, $montant_annuel
        ]);
    }

    // ─────────────────────────────────────────
    // Lister toutes les scolarités d'une école
    // pour une année donnée
    // ─────────────────────────────────────────
    public function findByAnnee($ecole_id, $annee_id) {
        $stmt = $this->db->prepare(
            "SELECT s.*, c.nom AS classe_nom, a.libelle AS annee_libelle
             FROM scolarites s
             JOIN classes c ON c.id = s.classe_id
             JOIN annees a ON a.id = s.annee_id
             WHERE s.ecole_id = ? AND s.annee_id = ?
             ORDER BY c.nom ASC"
        );
        $stmt->execute([$ecole_id, $annee_id]);
        return $stmt->fetchAll();
    }

    // ─────────────────────────────────────────
    // Récupérer la scolarité d'une classe
    // pour une année donnée
    // ─────────────────────────────────────────
    public function findByClasseAnnee($ecole_id, $classe_id, $annee_id) {
        $stmt = $this->db->prepare(
            "SELECT s.*, c.nom AS classe_nom, a.libelle AS annee_libelle
             FROM scolarites s
             JOIN classes c ON c.id = s.classe_id
             JOIN annees a ON a.id = s.annee_id
             WHERE s.ecole_id = ? AND s.classe_id = ? AND s.annee_id = ?
             LIMIT 1"
        );
        $stmt->execute([$ecole_id, $classe_id, $annee_id]);
        return $stmt->fetch();
    }

    // ─────────────────────────────────────────
    // Trouver une scolarité par ID
    // ─────────────────────────────────────────
    public function findById($id) {
        $stmt = $this->db->prepare(
            "SELECT s.*, c.nom AS classe_nom, a.libelle AS annee_libelle
             FROM scolarites s
             JOIN classes c ON c.id = s.classe_id
             JOIN annees a ON a.id = s.annee_id
             WHERE s.id = ?
             LIMIT 1"
        );
        $stmt->execute([$id]);
        return $stmt->fetch();
    }

    // ─────────────────────────────────────────
    // Supprimer une scolarité
    // ─────────────────────────────────────────
    public function delete($id, $ecole_id) {
        $stmt = $this->db->prepare(
            "DELETE FROM scolarites WHERE id = ? AND ecole_id = ?"
        );
        return $stmt->execute([$id, $ecole_id]);
    }

    // ─────────────────────────────────────────
    // Calculer le statut de paiement
    // ─────────────────────────────────────────
    public function calculerStatut($total_paye, $montant_annuel) {
        if ($total_paye <= 0)                return 'impaye';
        if ($total_paye >= $montant_annuel)  return 'paye';
        return 'partiel';
    }

    // ─────────────────────────────────────────
    // Résumé financier par classe pour une année
    // Utilisé dans le dashboard admin
    // ─────────────────────────────────────────
    public function getResumeFinancierByClasse($ecole_id, $annee_id) {
        $stmt = $this->db->prepare(
            "SELECT
                c.nom AS classe_nom,
                COUNT(DISTINCT i.id) AS nb_eleves,
                COALESCE(s.montant_annuel, 0) AS montant_annuel,
                COUNT(DISTINCT i.id) * COALESCE(s.montant_annuel, 0) AS total_attendu,
                COALESCE(SUM(p.montant), 0) AS total_paye,
                (COUNT(DISTINCT i.id) * COALESCE(s.montant_annuel, 0))
                    - COALESCE(SUM(p.montant), 0) AS reste_a_payer
             FROM classes c
             JOIN inscriptions i ON i.classe_id = c.id
                AND i.annee_id = ? AND i.ecole_id = ?
             LEFT JOIN scolarites s ON s.classe_id = c.id
                AND s.annee_id = ? AND s.ecole_id = ?
             LEFT JOIN paiements p ON p.inscription_id = i.id
             WHERE c.ecole_id = ?
             GROUP BY c.id, c.nom, s.montant_annuel
             ORDER BY c.nom ASC"
        );
        $stmt->execute([$annee_id, $ecole_id, $annee_id, $ecole_id, $ecole_id]);
        return $stmt->fetchAll();
    }

    // ─────────────────────────────────────────
    // Liste des élèves avec statut paiement
    // pour une classe et une année
    // Utilisé pour voir qui a payé ou pas
    // ─────────────────────────────────────────
    public function getElevesAvecStatutPaiement($ecole_id, $classe_id, $annee_id) {
        $stmt = $this->db->prepare(
            "SELECT
                e.id AS eleve_id,
                e.matricule,
                CONCAT(e.nom, ' ', e.prenom) AS nom_eleve,
                e.tel_tuteur,
                i.id AS inscription_id,
                COALESCE(s.montant_annuel, 0) AS montant_annuel,
                COALESCE(SUM(p.montant), 0) AS total_paye,
                COALESCE(s.montant_annuel, 0)
                    - COALESCE(SUM(p.montant), 0) AS reste,
                CASE
                    WHEN COALESCE(SUM(p.montant), 0) = 0 THEN 'impaye'
                    WHEN COALESCE(SUM(p.montant), 0) >= COALESCE(s.montant_annuel, 0) THEN 'paye'
                    ELSE 'partiel'
                END AS statut
             FROM inscriptions i
             JOIN eleves e ON e.id = i.eleve_id
             LEFT JOIN scolarites s ON s.classe_id = i.classe_id
                AND s.annee_id = i.annee_id
                AND s.ecole_id = i.ecole_id
             LEFT JOIN paiements p ON p.inscription_id = i.id
             WHERE i.ecole_id = ? AND i.classe_id = ? AND i.annee_id = ?
             GROUP BY i.id, e.id, e.matricule, e.nom, e.prenom,
                      e.tel_tuteur, s.montant_annuel
             ORDER BY e.nom ASC"
        );
        $stmt->execute([$ecole_id, $classe_id, $annee_id]);
        return $stmt->fetchAll();
    }

    // ─────────────────────────────────────────
    // Vérifier si scolarité existe déjà
    // ─────────────────────────────────────────
    public function exists($ecole_id, $classe_id, $annee_id) {
        $stmt = $this->db->prepare(
            "SELECT COUNT(*) FROM scolarites
             WHERE ecole_id = ? AND classe_id = ? AND annee_id = ?"
        );
        $stmt->execute([$ecole_id, $classe_id, $annee_id]);
        return $stmt->fetchColumn() > 0;
    }

    // ─────────────────────────────────────────
    // Compter les scolarités d'une école
    // ─────────────────────────────────────────
    public function countByEcole($ecoleId) {
        $stmt = $this->db->prepare(
            "SELECT COUNT(*) FROM scolarites WHERE ecole_id = ?"
        );
        $stmt->execute([$ecoleId]);
        return $stmt->fetchColumn();
    }
}