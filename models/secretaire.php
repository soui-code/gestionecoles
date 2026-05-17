<?php
// models/Secretaire.php

require_once 'config/Database.php';

class Secretaire {
    private $db;

    public function __construct() {
        $database = new Database();
        $this->db = $database->getConnection();
    }

    // ─────────────────────────────────────────
    // TABLEAU DE BORD — Stats globales
    // pour l'école de la secrétaire
    // ─────────────────────────────────────────
    public function getDashboardStats($ecole_id, $annee_id) {
        $stmt = $this->db->prepare(
            "SELECT
                COUNT(DISTINCT i.id)                             AS total_inscrits,
                COUNT(DISTINCT CASE
                    WHEN COALESCE(tot.total_paye, 0) >= COALESCE(s.montant_annuel, 0)
                         AND s.montant_annuel > 0
                    THEN i.id END)                               AS total_soldés,
                COUNT(DISTINCT CASE
                    WHEN COALESCE(tot.total_paye, 0) > 0
                         AND COALESCE(tot.total_paye, 0) < COALESCE(s.montant_annuel, 0)
                    THEN i.id END)                               AS total_partiels,
                COUNT(DISTINCT CASE
                    WHEN COALESCE(tot.total_paye, 0) = 0
                    THEN i.id END)                               AS total_impayes,
                COALESCE(SUM(tot.total_paye), 0)                AS montant_encaisse,
                COALESCE(
                    SUM(COALESCE(s.montant_annuel, 0))
                  - SUM(COALESCE(tot.total_paye, 0)), 0
                )                                                AS montant_restant
             FROM inscriptions i
             LEFT JOIN scolarites s
                    ON s.classe_id = i.classe_id
                   AND s.annee_id  = i.annee_id
                   AND s.ecole_id  = i.ecole_id
             LEFT JOIN (
                 SELECT inscription_id, SUM(montant) AS total_paye
                 FROM paiements
                 GROUP BY inscription_id
             ) tot ON tot.inscription_id = i.id
             WHERE i.ecole_id = ? AND i.annee_id = ?"
        );
        $stmt->execute([$ecole_id, $annee_id]);
        return $stmt->fetch();
    }

    // ─────────────────────────────────────────
    // LISTE DES PAIEMENTS — par école + année
    // avec détail élève/classe
    // ─────────────────────────────────────────
    public function getPaiementsParAnnee($ecole_id, $annee_id, $search = '') {
        $sql = "SELECT
                    p.id            AS paiement_id,
                    p.montant,
                    p.date_paiement,
                    p.statut,
                    i.id            AS inscription_id,
                    e.id            AS eleve_id,
                    e.matricule,
                    CONCAT(e.nom, ' ', e.prenom) AS nom_eleve,
                    c.nom           AS classe_nom,
                    a.libelle       AS annee_libelle,
                    f.numero_facture,
                    f.montant_scolarite,
                    f.montant_reste
                FROM paiements p
                JOIN inscriptions i  ON p.inscription_id = i.id
                JOIN eleves e        ON i.eleve_id = e.id
                JOIN classes c       ON i.classe_id = c.id
                JOIN annees a        ON i.annee_id  = a.id
                LEFT JOIN factures f ON f.paiement_id = p.id
                WHERE p.ecole_id = ? AND i.annee_id = ?";

        $params = [$ecole_id, $annee_id];

        if (!empty($search)) {
            $sql .= " AND (e.nom LIKE ? OR e.prenom LIKE ? OR e.matricule LIKE ? OR f.numero_facture LIKE ?)";
            $like = '%' . $search . '%';
            $params = array_merge($params, [$like, $like, $like, $like]);
        }

        $sql .= " ORDER BY p.date_paiement DESC";

        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    // ─────────────────────────────────────────
    // SUIVI SOLDES — liste des élèves inscrits
    // avec statut paiement complet
    // ─────────────────────────────────────────
    public function getSuiviSoldesParClasse($ecole_id, $annee_id, $classe_id = null, $statut = null, $search = '') {
        $sql = "SELECT
                    e.id            AS eleve_id,
                    e.matricule,
                    CONCAT(e.nom, ' ', e.prenom) AS nom_eleve,
                    e.tel_tuteur,
                    e.nom_tuteur,
                    i.id            AS inscription_id,
                    c.id            AS classe_id,
                    c.nom           AS classe_nom,
                    COALESCE(s.montant_annuel, 0)      AS montant_annuel,
                    COALESCE(tot.total_paye, 0)         AS total_paye,
                    COALESCE(s.montant_annuel, 0)
                  - COALESCE(tot.total_paye, 0)         AS reste,
                    CASE
                        WHEN COALESCE(tot.total_paye, 0) = 0                                 THEN 'impaye'
                        WHEN COALESCE(tot.total_paye, 0) >= COALESCE(s.montant_annuel, 0)
                             AND s.montant_annuel > 0                                         THEN 'solde'
                        ELSE 'partiel'
                    END AS statut_paiement,
                    (SELECT MAX(p2.date_paiement)
                     FROM paiements p2
                     WHERE p2.inscription_id = i.id)   AS dernier_paiement
                FROM inscriptions i
                JOIN eleves e  ON i.eleve_id  = e.id
                JOIN classes c ON i.classe_id = c.id
                LEFT JOIN scolarites s
                       ON s.classe_id = i.classe_id
                      AND s.annee_id  = i.annee_id
                      AND s.ecole_id  = i.ecole_id
                LEFT JOIN (
                    SELECT inscription_id, SUM(montant) AS total_paye
                    FROM paiements
                    GROUP BY inscription_id
                ) tot ON tot.inscription_id = i.id
                WHERE i.ecole_id = ? AND i.annee_id = ?";

        $params = [$ecole_id, $annee_id];

        if ($classe_id) {
            $sql .= " AND i.classe_id = ?";
            $params[] = $classe_id;
        }

        if ($statut && in_array($statut, ['solde', 'partiel', 'impaye'])) {
            $having = [
                'solde'   => "COALESCE(tot.total_paye,0) >= COALESCE(s.montant_annuel,0) AND s.montant_annuel > 0",
                'partiel' => "COALESCE(tot.total_paye,0) > 0 AND COALESCE(tot.total_paye,0) < COALESCE(s.montant_annuel,0)",
                'impaye'  => "COALESCE(tot.total_paye,0) = 0",
            ];
            $sql .= " AND (" . $having[$statut] . ")";
        }

        if (!empty($search)) {
            $sql .= " AND (e.nom LIKE ? OR e.prenom LIKE ? OR e.matricule LIKE ?)";
            $like = '%' . $search . '%';
            $params = array_merge($params, [$like, $like, $like]);
        }

        $sql .= " ORDER BY c.nom ASC, e.nom ASC";

        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    // ─────────────────────────────────────────
    // REÇU COMPLET — données pour impression
    // ─────────────────────────────────────────
    public function getRecuComplet($paiement_id, $ecole_id) {
        $stmt = $this->db->prepare(
            "SELECT
                p.id            AS paiement_id,
                p.montant       AS montant_verse,
                p.date_paiement,
                p.statut,
                f.numero_facture,
                f.montant_scolarite,
                f.montant_paye,
                f.montant_reste,
                f.nom_eleve,
                f.classe_nom,
                f.annee_libelle,
                e_row.matricule,
                e_row.date_naissance,
                e_row.lieu_naissance,
                e_row.nom_pere,
                e_row.nom_mere,
                e_row.tel_tuteur,
                e_row.nom_tuteur,
                ec.nom          AS ecole_nom,
                ec.adresse      AS ecole_adresse,
                ec.telephone    AS ecole_tel,
                ec.email        AS ecole_email,
                i.id            AS inscription_id,
                a.libelle       AS annee_libelle_raw
             FROM paiements p
             JOIN inscriptions i  ON p.inscription_id = i.id
             JOIN eleves e_row    ON i.eleve_id = e_row.id
             JOIN annees a        ON i.annee_id = a.id
             JOIN ecoles ec       ON p.ecole_id = ec.id
             LEFT JOIN factures f ON f.paiement_id = p.id
             WHERE p.id = ? AND p.ecole_id = ?
             LIMIT 1"
        );
        $stmt->execute([$paiement_id, $ecole_id]);
        return $stmt->fetch();
    }

    // ─────────────────────────────────────────
    // HISTORIQUE PAIEMENTS D'UN ÉLÈVE
    // ─────────────────────────────────────────
    public function getHistoriquePaiementsEleve($inscription_id) {
        $stmt = $this->db->prepare(
            "SELECT
                p.*,
                f.numero_facture,
                f.montant_scolarite,
                f.montant_reste
             FROM paiements p
             LEFT JOIN factures f ON f.paiement_id = p.id
             WHERE p.inscription_id = ?
             ORDER BY p.date_paiement DESC"
        );
        $stmt->execute([$inscription_id]);
        return $stmt->fetchAll();
    }

    // ─────────────────────────────────────────
    // AJOUTER UN PAIEMENT COMPLÉMENTAIRE
    // retourne le nouveau paiement avec facture
    // ─────────────────────────────────────────
    public function ajouterPaiement($ecole_id, $inscription_id, $montant, $date_paiement, $statut, $annee_id) {
        // 1. Insérer le paiement
        $stmt = $this->db->prepare(
            "INSERT INTO paiements (ecole_id, inscription_id, montant, date_paiement, statut)
             VALUES (?, ?, ?, ?, ?)"
        );
        $stmt->execute([$ecole_id, $inscription_id, $montant, $date_paiement, $statut]);
        $paiement_id = $this->db->lastInsertId();

        // 2. Générer numéro de facture
        $stmt2 = $this->db->prepare(
            "SELECT COUNT(*) FROM factures
             WHERE ecole_id = ? AND inscription_id IN (
                SELECT id FROM inscriptions WHERE annee_id = ?
             )"
        );
        $stmt2->execute([$ecole_id, $annee_id]);
        $count = $stmt2->fetchColumn();

        $stmtAnnee = $this->db->prepare("SELECT libelle FROM annees WHERE id = ?");
        $stmtAnnee->execute([$annee_id]);
        $anneeLibelle = explode('-', $stmtAnnee->fetchColumn())[0];
        $numero_facture = 'FAC-' . $ecole_id . '-' . $anneeLibelle . '-' . str_pad($count + 1, 4, '0', STR_PAD_LEFT);

        // 3. Récupérer info inscription pour la facture
        $stmtInsc = $this->db->prepare(
            "SELECT i.*,
                    CONCAT(e.nom, ' ', e.prenom) AS nom_eleve,
                    c.nom AS classe_nom,
                    a.libelle AS annee_libelle,
                    COALESCE(s.montant_annuel, 0) AS montant_scolarite
             FROM inscriptions i
             JOIN eleves e ON i.eleve_id = e.id
             JOIN classes c ON i.classe_id = c.id
             JOIN annees a ON i.annee_id = a.id
             LEFT JOIN scolarites s ON s.classe_id = i.classe_id
                AND s.annee_id = i.annee_id AND s.ecole_id = i.ecole_id
             WHERE i.id = ? LIMIT 1"
        );
        $stmtInsc->execute([$inscription_id]);
        $insc = $stmtInsc->fetch();

        // 4. Total payé (nouveau total incluant ce paiement)
        $stmtTot = $this->db->prepare(
            "SELECT COALESCE(SUM(montant), 0) FROM paiements WHERE inscription_id = ?"
        );
        $stmtTot->execute([$inscription_id]);
        $total_paye = $stmtTot->fetchColumn();

        $montant_reste = max(0, $insc['montant_scolarite'] - $total_paye);

        // 5. Créer la facture
        $stmt3 = $this->db->prepare(
            "INSERT INTO factures (
                ecole_id, paiement_id, inscription_id,
                numero_facture, nom_eleve, classe_nom,
                annee_libelle, montant_scolarite,
                montant_paye, montant_reste, date_paiement
             ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)"
        );
        $stmt3->execute([
            $ecole_id, $paiement_id, $inscription_id,
            $numero_facture, $insc['nom_eleve'], $insc['classe_nom'],
            $insc['annee_libelle'], $insc['montant_scolarite'],
            $total_paye, $montant_reste, $date_paiement
        ]);

        return $paiement_id;
    }

    // ─────────────────────────────────────────
    // RECHERCHE RAPIDE D'UN ÉLÈVE
    // par matricule ou nom
    // ─────────────────────────────────────────
    public function rechercherEleve($ecole_id, $annee_id, $query) {
        $stmt = $this->db->prepare(
            "SELECT
                e.id, e.matricule,
                CONCAT(e.nom, ' ', e.prenom) AS nom_eleve,
                c.nom   AS classe_nom,
                i.id    AS inscription_id
             FROM eleves e
             JOIN inscriptions i ON i.eleve_id = e.id
             JOIN classes c      ON i.classe_id = c.id
             WHERE e.ecole_id = ? AND i.annee_id = ?
               AND (e.nom LIKE ? OR e.prenom LIKE ? OR e.matricule LIKE ?)
             ORDER BY e.nom ASC
             LIMIT 10"
        );
        $like = '%' . $query . '%';
        $stmt->execute([$ecole_id, $annee_id, $like, $like, $like]);
        return $stmt->fetchAll();
    }

    // ─────────────────────────────────────────
    // FICHE SOLDE D'UN ÉLÈVE
    // détail complet pour 1 inscription
    // ─────────────────────────────────────────
    public function getFicheSolde($inscription_id, $ecole_id) {
        $stmt = $this->db->prepare(
            "SELECT
                i.id            AS inscription_id,
                e.id            AS eleve_id,
                e.matricule,
                CONCAT(e.nom, ' ', e.prenom) AS nom_eleve,
                e.date_naissance,
                e.lieu_naissance,
                e.domicile,
                e.tel_tuteur,
                e.nom_tuteur,
                e.nom_pere,
                e.nom_mere,
                e.tel_pere,
                e.tel_mere,
                c.nom           AS classe_nom,
                a.libelle       AS annee_libelle,
                COALESCE(s.montant_annuel, 0)      AS montant_annuel,
                COALESCE(s.montant_mensuel, 0)     AS montant_mensuel,
                COALESCE(s.montant_inscription, 0) AS montant_inscription,
                COALESCE(tot.total_paye, 0)         AS total_paye,
                COALESCE(s.montant_annuel, 0)
              - COALESCE(tot.total_paye, 0)         AS reste_a_payer,
                CASE
                    WHEN COALESCE(tot.total_paye, 0) = 0                                  THEN 'impaye'
                    WHEN COALESCE(tot.total_paye, 0) >= COALESCE(s.montant_annuel, 0)
                         AND s.montant_annuel > 0                                          THEN 'solde'
                    ELSE 'partiel'
                END AS statut_paiement
             FROM inscriptions i
             JOIN eleves e  ON i.eleve_id  = e.id
             JOIN classes c ON i.classe_id = c.id
             JOIN annees a  ON i.annee_id  = a.id
             LEFT JOIN scolarites s
                    ON s.classe_id = i.classe_id
                   AND s.annee_id  = i.annee_id
                   AND s.ecole_id  = i.ecole_id
             LEFT JOIN (
                 SELECT inscription_id, SUM(montant) AS total_paye
                 FROM paiements GROUP BY inscription_id
             ) tot ON tot.inscription_id = i.id
             WHERE i.id = ? AND i.ecole_id = ?
             LIMIT 1"
        );
        $stmt->execute([$inscription_id, $ecole_id]);
        return $stmt->fetch();
    }

    // ─────────────────────────────────────────
    // STATS PAR CLASSE — résumé financier
    // ─────────────────────────────────────────
    public function getResumeParClasse($ecole_id, $annee_id) {
        $stmt = $this->db->prepare(
            "SELECT
                c.id            AS classe_id,
                c.nom           AS classe_nom,
                COUNT(DISTINCT i.id)                                    AS nb_inscrits,
                COALESCE(s.montant_annuel, 0)                           AS montant_annuel,
                COUNT(DISTINCT i.id) * COALESCE(s.montant_annuel, 0)   AS total_attendu,
                COALESCE(SUM(p.montant), 0)                             AS total_encaisse,
                (COUNT(DISTINCT i.id) * COALESCE(s.montant_annuel, 0))
              - COALESCE(SUM(p.montant), 0)                             AS total_reste,
                COUNT(DISTINCT CASE
                    WHEN COALESCE(tot.total_paye, 0) >= COALESCE(s.montant_annuel, 0)
                         AND s.montant_annuel > 0
                    THEN i.id END)                                       AS nb_soldes,
                COUNT(DISTINCT CASE
                    WHEN COALESCE(tot.total_paye, 0) > 0
                         AND COALESCE(tot.total_paye, 0) < COALESCE(s.montant_annuel, 0)
                    THEN i.id END)                                       AS nb_partiels,
                COUNT(DISTINCT CASE
                    WHEN COALESCE(tot.total_paye, 0) = 0
                    THEN i.id END)                                       AS nb_impayes
             FROM classes c
             JOIN inscriptions i ON i.classe_id = c.id
                AND i.annee_id = ? AND i.ecole_id = ?
             LEFT JOIN scolarites s
                    ON s.classe_id = c.id
                   AND s.annee_id  = ?
                   AND s.ecole_id  = ?
             LEFT JOIN paiements p ON p.inscription_id = i.id
             LEFT JOIN (
                 SELECT inscription_id, SUM(montant) AS total_paye
                 FROM paiements GROUP BY inscription_id
             ) tot ON tot.inscription_id = i.id
             WHERE c.ecole_id = ?
             GROUP BY c.id, c.nom, s.montant_annuel
             ORDER BY c.nom ASC"
        );
        $stmt->execute([$annee_id, $ecole_id, $annee_id, $ecole_id, $ecole_id]);
        return $stmt->fetchAll();
    }
}