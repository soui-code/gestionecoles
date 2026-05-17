<?php
// models/User.php

require_once 'config/Database.php';

class User {
    private $db;

    public function __construct() {
        $database = new Database();
        $this->db = $database->getConnection();
    }

    // ─────────────────────────────────────────
    // Lister tous les users
    // Utilisé par le super_admin
    // ─────────────────────────────────────────
    public function findAll() {
        $stmt = $this->db->query(
            "SELECT u.*, e.nom AS ecole_nom
             FROM users u
             LEFT JOIN ecoles e ON u.ecole_id = e.id
             ORDER BY u.role ASC, u.nom ASC"
        );
        return $stmt->fetchAll();
    }

    // ─────────────────────────────────────────
    // Lister les users d'une école
    // Utilisé par l'admin
    // ─────────────────────────────────────────
    public function findByEcole($ecoleId) {
        $stmt = $this->db->prepare(
            "SELECT u.*, e.nom AS ecole_nom
             FROM users u
             LEFT JOIN ecoles e ON u.ecole_id = e.id
             WHERE u.ecole_id = ?
             ORDER BY u.role ASC, u.nom ASC"
        );
        $stmt->execute([$ecoleId]);
        return $stmt->fetchAll();
    }

    // ─────────────────────────────────────────
    // Trouver un user par email
    // Utilisé au login
    // ─────────────────────────────────────────
    public function findByEmail($email) {
        $stmt = $this->db->prepare(
            "SELECT u.*, e.nom AS ecole_nom
             FROM users u
             LEFT JOIN ecoles e ON u.ecole_id = e.id
             WHERE u.email = ?
             LIMIT 1"
        );
        $stmt->execute([$email]);
        return $stmt->fetch();
    }

    // ─────────────────────────────────────────
    // Trouver un user par ID
    // ─────────────────────────────────────────
    public function findById($id) {
        $stmt = $this->db->prepare(
            "SELECT u.*, e.nom AS ecole_nom
             FROM users u
             LEFT JOIN ecoles e ON u.ecole_id = e.id
             WHERE u.id = ?
             LIMIT 1"
        );
        $stmt->execute([$id]);
        return $stmt->fetch();
    }

    // ─────────────────────────────────────────
    // Créer un user (super_admin ou admin)
    // ─────────────────────────────────────────
    public function create($ecole_id, $nom, $email, $password, $role) {
        $hashedPassword = password_hash($password, PASSWORD_BCRYPT);
        $stmt = $this->db->prepare(
            "INSERT INTO users (ecole_id, nom, email, password, role)
             VALUES (?, ?, ?, ?, ?)"
        );
        $stmt->execute([$ecole_id ?? null, $nom, $email, $hashedPassword, $role]);
        return $this->findById($this->db->lastInsertId());
    }

    // ─────────────────────────────────────────
    // Modifier un user
    // ─────────────────────────────────────────
    public function update($id, $ecole_id, $nom, $email, $role) {
        $stmt = $this->db->prepare(
            "UPDATE users
             SET ecole_id = ?, nom = ?, email = ?, role = ?
             WHERE id = ?"
        );
        return $stmt->execute([$ecole_id, $nom, $email, $role, $id]);
    }

    // ─────────────────────────────────────────
    // Mettre à jour le mot de passe
    // ─────────────────────────────────────────
    public function updatePassword($id, $newPassword) {
        $hashed = password_hash($newPassword, PASSWORD_BCRYPT);
        $stmt = $this->db->prepare(
            "UPDATE users SET password = ? WHERE id = ?"
        );
        return $stmt->execute([$hashed, $id]);
    }

    // ─────────────────────────────────────────
    // Supprimer un user
    // ─────────────────────────────────────────
    public function delete($id) {
        $stmt = $this->db->prepare(
            "DELETE FROM users WHERE id = ?"
        );
        return $stmt->execute([$id]);
    }

    // ─────────────────────────────────────────
    // Vérifier si email existe déjà
    // ─────────────────────────────────────────
    public function emailExists($email, $excludeId = null) {
        if ($excludeId) {
            $stmt = $this->db->prepare(
                "SELECT COUNT(*) FROM users
                 WHERE email = ? AND id != ?"
            );
            $stmt->execute([$email, $excludeId]);
        } else {
            $stmt = $this->db->prepare(
                "SELECT COUNT(*) FROM users
                 WHERE email = ?"
            );
            $stmt->execute([$email]);
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
    // Compter tous les users
    // ─────────────────────────────────────────
    public function count() {
        return $this->db->query(
            "SELECT COUNT(*) FROM users"
        )->fetchColumn();
    }

    // ─────────────────────────────────────────
    // Compter les users par rôle
    // ─────────────────────────────────────────
    public function countByRole($role) {
        $stmt = $this->db->prepare(
            "SELECT COUNT(*) FROM users WHERE role = ?"
        );
        $stmt->execute([$role]);
        return $stmt->fetchColumn();
    }

    // ─────────────────────────────────────────
    // Compter les admins d'une école
    // ─────────────────────────────────────────
    public function countByEcole($ecoleId) {
        $stmt = $this->db->prepare(
            "SELECT COUNT(*) FROM users
             WHERE ecole_id = ? AND role = 'admin'"
        );
        $stmt->execute([$ecoleId]);
        return $stmt->fetchColumn();
    }
}