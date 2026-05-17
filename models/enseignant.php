<?php
// models/Enseignant.php

require_once 'config/Database.php';

class Enseignant {
    private $db;

    public function __construct() {
        $database = new Database();
        $this->db = $database->getConnection();
    }

    // ─────────────────────────────────────────
    // Trouver un enseignant par email
    // ─────────────────────────────────────────
    public function findByEmail($email) {
        $stmt = $this->db->prepare(
            "SELECT e.*, ec.nom AS ecole_nom
             FROM enseignants e
             LEFT JOIN ecoles ec ON e.ecole_id = ec.id
             WHERE e.email = ?
             LIMIT 1"
        );
        $stmt->execute([$email]);
        return $stmt->fetch();
    }

    // ─────────────────────────────────────────
    // Trouver un enseignant par ID
    // ─────────────────────────────────────────
    public function findById($id) {
        $stmt = $this->db->prepare(
            "SELECT e.*, ec.nom AS ecole_nom
             FROM enseignants e
             LEFT JOIN ecoles ec ON e.ecole_id = ec.id
             WHERE e.id = ?
             LIMIT 1"
        );
        $stmt->execute([$id]);
        return $stmt->fetch();
    }

    // ─────────────────────────────────────────
    // Lister tous les enseignants
    // Utilisé par le super_admin
    // ─────────────────────────────────────────
    public function findAll() {
        $stmt = $this->db->query(
            "SELECT e.*, ec.nom AS ecole_nom
             FROM enseignants e
             LEFT JOIN ecoles ec ON e.ecole_id = ec.id
             ORDER BY ec.nom ASC, e.nom ASC"
        );
        return $stmt->fetchAll();
    }

    // ─────────────────────────────────────────
    // Lister les enseignants d'une école
    // Utilisé par l'admin
    // ─────────────────────────────────────────
    public function findByEcole($ecoleId) {
        $stmt = $this->db->prepare(
            "SELECT e.*, ec.nom AS ecole_nom
             FROM enseignants e
             LEFT JOIN ecoles ec ON e.ecole_id = ec.id
             WHERE e.ecole_id = ?
             ORDER BY e.role ASC, e.nom ASC"
        );
        $stmt->execute([$ecoleId]);
        return $stmt->fetchAll();
    }

    // ─────────────────────────────────────────
    // Lister uniquement les enseignants d'une école
    // Utilisé pour affecter à une classe
    // ─────────────────────────────────────────
    public function findEnseignantsByEcole($ecoleId) {
        $stmt = $this->db->prepare(
            "SELECT e.id, e.nom, e.prenom, e.email, e.tel
             FROM enseignants e
             WHERE e.ecole_id = ? AND e.role = 'enseignant'
             ORDER BY e.nom ASC"
        );
        $stmt->execute([$ecoleId]);
        return $stmt->fetchAll();
    }

    // ─────────────────────────────────────────
    // Créer un enseignant ou secrétaire
    // ─────────────────────────────────────────
    public function create($ecole_id, $nom, $prenom, $email, $tel, $password, $role) {
        $hashedPassword = password_hash($password, PASSWORD_BCRYPT);
        $stmt = $this->db->prepare(
            "INSERT INTO enseignants (ecole_id, nom, prenom, email, tel, password, role)
             VALUES (?, ?, ?, ?, ?, ?, ?)"
        );
        $stmt->execute([$ecole_id, $nom, $prenom, $email, $tel, $hashedPassword, $role]);
        return $this->findById($this->db->lastInsertId());
    }

    // ─────────────────────────────────────────
    // Modifier un enseignant ou secrétaire
    // ─────────────────────────────────────────
    public function update($id, $nom, $prenom, $email, $tel, $role) {
        $stmt = $this->db->prepare(
            "UPDATE enseignants
             SET nom = ?, prenom = ?, email = ?, tel = ?, role = ?
             WHERE id = ?"
        );
        return $stmt->execute([$nom, $prenom, $email, $tel, $role, $id]);
    }

    // ─────────────────────────────────────────
    // Mettre à jour le mot de passe
    // ─────────────────────────────────────────
    public function updatePassword($id, $newPassword) {
        $hashed = password_hash($newPassword, PASSWORD_BCRYPT);
        $stmt = $this->db->prepare(
            "UPDATE enseignants SET password = ? WHERE id = ?"
        );
        return $stmt->execute([$hashed, $id]);
    }

    // ─────────────────────────────────────────
    // Supprimer un enseignant
    // ─────────────────────────────────────────
    public function delete($id) {
        $stmt = $this->db->prepare(
            "DELETE FROM enseignants WHERE id = ?"
        );
        return $stmt->execute([$id]);
    }

    // ─────────────────────────────────────────
    // Vérifier si email existe déjà
    // ─────────────────────────────────────────
    public function emailExists($email, $excludeId = null) {
        if ($excludeId) {
            $stmt = $this->db->prepare(
                "SELECT COUNT(*) FROM enseignants WHERE email = ? AND id != ?"
            );
            $stmt->execute([$email, $excludeId]);
        } else {
            $stmt = $this->db->prepare(
                "SELECT COUNT(*) FROM enseignants WHERE email = ?"
            );
            $stmt->execute([$email]);
        }
        return $stmt->fetchColumn() > 0;
    }

    // ─────────────────────────────────────────
    // Vérifier si tel existe déjà
    // ─────────────────────────────────────────
    public function telExists($tel, $excludeId = null) {
        if ($excludeId) {
            $stmt = $this->db->prepare(
                "SELECT COUNT(*) FROM enseignants WHERE tel = ? AND id != ?"
            );
            $stmt->execute([$tel, $excludeId]);
        } else {
            $stmt = $this->db->prepare(
                "SELECT COUNT(*) FROM enseignants WHERE tel = ?"
            );
            $stmt->execute([$tel]);
        }
        return $stmt->fetchColumn() > 0;
    }

    // ─────────────────────────────────────────
    // Vérifier le mot de passe
    // ─────────────────────────────────────────
    public function verifyPassword($plainPassword, $hashedPassword) {
        return password_verify($plainPassword, $hashedPassword);
    }

    // ─────────────────────────────────────────
    // Supprimer le mot de passe avant usage
    // ─────────────────────────────────────────
    public function sanitize($user) {
        if (!$user) return null;
        unset($user['password']);
        return $user;
    }

    // ─────────────────────────────────────────
    // Compter les enseignants d'une école
    // ─────────────────────────────────────────
    public function countByEcole($ecoleId) {
        $stmt = $this->db->prepare(
            "SELECT COUNT(*) FROM enseignants WHERE ecole_id = ? AND role = 'enseignant'"
        );
        $stmt->execute([$ecoleId]);
        return $stmt->fetchColumn();
    }

    // ─────────────────────────────────────────
    // Compter les secrétaires d'une école
    // ─────────────────────────────────────────
    public function countSecretairesByEcole($ecoleId) {
        $stmt = $this->db->prepare(
            "SELECT COUNT(*) FROM enseignants WHERE ecole_id = ? AND role = 'secretaire'"
        );
        $stmt->execute([$ecoleId]);
        return $stmt->fetchColumn();
    }
    // Compter tous les enseignants ou secretaires
public function countAll($role = null) {
    if ($role) {
        $stmt = $this->db->prepare(
            "SELECT COUNT(*) FROM enseignants WHERE role = ?"
        );
        $stmt->execute([$role]);
    } else {
        $stmt = $this->db->query(
            "SELECT COUNT(*) FROM enseignants"
        );
    }
    return $stmt->fetchColumn();
}
}