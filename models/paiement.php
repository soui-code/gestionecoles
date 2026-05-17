<?php
// models/Paiement.php

require_once 'config/Database.php';

class Paiement {
    private $db;

    public function __construct() {
        $database = new Database();
        $this->db = $database->getConnection();
    }

    // ─────────────────────────────────────────
    // Lister tous les paiements
    // Utilisé par le super_admin
    // ─────────────────────────────────────────
    public function findAll() {
        $stmt = $this->db->query(
            "SELECT p.*,
                    e.nom AS eleve_nom, e.prenom AS eleve_prenom,
                    e.matricule,
                    c.nom AS classe_nom,
                    a.libelle AS annee_libelle,
                    ec.nom AS ecole_nom
             FROM paiements p
             JOIN inscriptions i ON p.inscription_id = i.id
             JOIN eleves e ON i.eleve_id = e.id
             JOIN classes c ON i.classe_id = c.id
             JOIN annees a ON i.annee_id = a.id
             JOIN ecoles ec ON i.ecole_id = ec.id
             ORDER BY p.date_paiement DESC"
        );
        return $stmt->fetchAll();
    }

    // ─────────────────────────────────────────
    // Lister les paiements d'une école
    // ─────────────────────────────────────────
    public function findByEcole($ecoleId) {
        $stmt = $this->db->prepare(
            "SELECT p.*,
                    e.nom AS eleve_nom, e.prenom AS eleve_prenom,
                    e.matricule,
                    c.nom AS classe_nom,
                    a.libelle AS annee_libelle
             FROM paiements p
             JOIN inscriptions i ON p.inscription_id = i.id
             JOIN eleves e ON i.eleve_id = e.id
             JOIN classes c ON i.classe_id = c.id
             JOIN annees a ON i.annee_id = a.id
             WHERE p.ecole_id = ?
             ORDER BY p.date_paiement DESC"
        );
        $stmt->execute([$ecoleId]);
        return $stmt->fetchAll();
    }

    // ─────────────────────────────────────────
    // Lister les paiements par inscription
    // ─────────────────────────────────────────
    public function findByInscription($inscriptionId) {
        $stmt = $this->db->prepare(
            "SELECT p.*,
                    e.nom AS eleve_nom, e.prenom AS eleve_prenom,
                    e.matricule,
                    c.nom AS classe_nom,
                    a.libelle AS annee_libelle
             FROM paiements p
             JOIN inscriptions i ON p.inscription_id = i.id
             JOIN eleves e ON i.eleve_id = e.id
             JOIN classes c ON i.classe_id = c.id
             JOIN annees a ON i.annee_id = a.id
             WHERE p.inscription_id = ?
             ORDER BY p.date_paiement DESC"
        );
        $stmt->execute([$inscriptionId]);
        return $stmt->fetchAll();
    }

    // ─────────────────────────────────────────
    // Lister les paiements par année
    // ─────────────────────────────────────────
    public function findByAnnee($ecoleId, $anneeId) {
        $stmt = $this->db->prepare(
            "SELECT p.*,
                    e.nom AS eleve_nom, e.prenom AS eleve_prenom,
                    e.matricule,
                    c.nom AS classe_nom,
                    a.libelle AS annee_libelle
             FROM paiements p
             JOIN inscriptions i ON p.inscription_id = i.id
             JOIN eleves e ON i.eleve_id = e.id
             JOIN classes c ON i.classe_id = c.id
             JOIN annees a ON i.annee_id = a.id
             WHERE p.ecole_id = ? AND i.annee_id = ?
             ORDER BY p.date_paiement DESC"
        );
        $stmt->execute([$ecoleId, $anneeId]);
        return $stmt->fetchAll();
    }

    // ─────────────────────────────────────────
    // Trouver un paiement par ID
    // ─────────────────────────────────────────
    public function findById($id) {
        $stmt = $this->db->prepare(
            "SELECT p.*,
                    e.nom AS eleve_nom, e.prenom AS eleve_prenom,
                    e.matricule,
                    c.nom AS classe_nom,
                    a.libelle AS annee_libelle,
                    ec.nom AS ecole_nom
             FROM paiements p
             JOIN inscriptions i ON p.inscription_id = i.id
             JOIN eleves e ON i.eleve_id = e.id
             JOIN classes c ON i.classe_id = c.id
             JOIN annees a ON i.annee_id = a.id
             JOIN ecoles ec ON i.ecole_id = ec.id
             WHERE p.id = ?
             LIMIT 1"
        );
        $stmt->execute([$id]);
        return $stmt->fetch();
    }

    // ─────────────────────────────────────────
    // Créer un paiement
    // ─────────────────────────────────────────
    public function create($ecole_id, $inscription_id, $montant, $date_paiement, $statut) {
        $stmt = $this->db->prepare(
            "INSERT INTO paiements (ecole_id, inscription_id, montant, date_paiement, statut)
             VALUES (?, ?, ?, ?, ?)"
        );
        $stmt->execute([$ecole_id, $inscription_id, $montant, $date_paiement, $statut]);
        return $this->findById($this->db->lastInsertId());
    }

    // ─────────────────────────────────────────
    // Supprimer un paiement
    // ─────────────────────────────────────────
    public function delete($id) {
        $stmt = $this->db->prepare(
            "DELETE FROM paiements WHERE id = ?"
        );
        return $stmt->execute([$id]);
    }

    // ─────────────────────────────────────────
    // Calculer total payé pour une inscription
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
    // Générer numéro de facture
    // Format : FAC-{ecole_id}-{annee}-{numero}
    // ─────────────────────────────────────────
    public function genererNumeroFacture($ecoleId, $anneeId) {
        $stmt = $this->db->prepare(
            "SELECT COUNT(*) FROM factures
             WHERE ecole_id = ? AND inscription_id IN (
                SELECT id FROM inscriptions WHERE annee_id = ?
             )"
        );
        $stmt->execute([$ecoleId, $anneeId]);
        $count = $stmt->fetchColumn();

        // Format : FAC-1-2024-0001
        $annee = $this->db->prepare(
            "SELECT libelle FROM annees WHERE id = ?"
        );
        $annee->execute([$anneeId]);
        $anneeLibelle = explode('-', $annee->fetchColumn())[0];

        return 'FAC-' . $ecoleId . '-' . $anneeLibelle . '-' . str_pad($count + 1, 4, '0', STR_PAD_LEFT);
    }

    // ─────────────────────────────────────────
    // Créer une facture
    // ─────────────────────────────────────────
    public function creerFacture(
        $ecole_id, $paiement_id, $inscription_id,
        $numero_facture, $nom_eleve, $classe_nom,
        $annee_libelle, $montant_scolarite,
        $montant_paye, $montant_reste, $date_paiement
    ) {
        $stmt = $this->db->prepare(
            "INSERT INTO factures (
                ecole_id, paiement_id, inscription_id,
                numero_facture, nom_eleve, classe_nom,
                annee_libelle, montant_scolarite,
                montant_paye, montant_reste, date_paiement
             ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)"
        );
        return $stmt->execute([
            $ecole_id, $paiement_id, $inscription_id,
            $numero_facture, $nom_eleve, $classe_nom,
            $annee_libelle, $montant_scolarite,
            $montant_paye, $montant_reste, $date_paiement
        ]);
    }

    // ─────────────────────────────────────────
    // Trouver une facture par paiement
    // ─────────────────────────────────────────
    public function findFacture($paiementId) {
        $stmt = $this->db->prepare(
            "SELECT * FROM factures WHERE paiement_id = ? LIMIT 1"
        );
        $stmt->execute([$paiementId]);
        return $stmt->fetch();
    }

    // ─────────────────────────────────────────
    // Trouver une facture par numéro
    // ─────────────────────────────────────────
    public function findFactureByNumero($numero) {
        $stmt = $this->db->prepare(
            "SELECT * FROM factures WHERE numero_facture = ? LIMIT 1"
        );
        $stmt->execute([$numero]);
        return $stmt->fetch();
    }

    // ─────────────────────────────────────────
    // Lister les factures d'une école
    // ─────────────────────────────────────────
    public function findFacturesByEcole($ecoleId) {
        $stmt = $this->db->prepare(
            "SELECT * FROM factures
             WHERE ecole_id = ?
             ORDER BY created_at DESC"
        );
        $stmt->execute([$ecoleId]);
        return $stmt->fetchAll();
    }

    // ─────────────────────────────────────────
    // Stats paiements d'une école
    // ─────────────────────────────────────────
    public function statsByEcole($ecoleId, $anneeId) {
        $stmt = $this->db->prepare(
            "SELECT
                COUNT(*) AS total_paiements,
                SUM(p.montant) AS total_encaisse,
                SUM(CASE WHEN p.statut = 'paye' THEN 1 ELSE 0 END) AS total_paye,
                SUM(CASE WHEN p.statut = 'partiel' THEN 1 ELSE 0 END) AS total_partiel,
                SUM(CASE WHEN p.statut = 'impaye' THEN 1 ELSE 0 END) AS total_impaye
             FROM paiements p
             JOIN inscriptions i ON p.inscription_id = i.id
             WHERE p.ecole_id = ? AND i.annee_id = ?"
        );
        $stmt->execute([$ecoleId, $anneeId]);
        return $stmt->fetch();
    }
}