<?php
// middleware/auth.php

class Auth {

    // ─────────────────────────────────────────
    // Vérifier si connecté
    // ─────────────────────────────────────────
    public static function check() {
        if (!isset($_SESSION['user_id'])) {
            header('Location: /gestionecole/auth/login');
            exit;
        }
    }

    // ─────────────────────────────────────────
    // Vérifier le rôle
    // ─────────────────────────────────────────
    public static function role(...$roles) {
        self::check();
        if (!in_array($_SESSION['user_role'], $roles)) {
            header('Location: /gestionecole/auth/login');
            exit;
        }
    }

    // ─────────────────────────────────────────
    // Récupérer l'utilisateur connecté
    // ─────────────────────────────────────────
    public static function user() {
        return [
            'id'        => $_SESSION['user_id']   ?? null,
            'nom'       => $_SESSION['user_nom']  ?? null,
            'role'      => $_SESSION['user_role'] ?? null,
            'ecole_id'  => $_SESSION['ecole_id']  ?? null,
            'ecole_nom' => $_SESSION['ecole_nom'] ?? null,
        ];
    }

    // ─────────────────────────────────────────
    // Helpers
    // ─────────────────────────────────────────
    public static function getRole() {
        return $_SESSION['user_role'] ?? null;
    }

    public static function getEcoleId() {
        return $_SESSION['ecole_id'] ?? null;
    }

    public static function isSuperAdmin() {
        return $_SESSION['user_role'] === 'super_admin';
    }

    public static function isAdmin() {
        return $_SESSION['user_role'] === 'admin';
    }

    public static function isEnseignant() {
        return $_SESSION['user_role'] === 'enseignant';
    }

    public static function isSecretaire() {
        return $_SESSION['user_role'] === 'secretaire';
    }
}