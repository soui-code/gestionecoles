-- ─────────────────────────────────────────
-- CREATION BASE DE DONNÉES
-- ─────────────────────────────────────────
CREATE DATABASE IF NOT EXISTS gestionecole;
USE gestionecole;

-- ─────────────────────────────────────────
-- TABLE ecoles
-- ─────────────────────────────────────────
CREATE TABLE ecoles (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nom VARCHAR(150) NOT NULL,
    adresse VARCHAR(255),
    telephone VARCHAR(50),
    email VARCHAR(100),
    date_creation TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- ─────────────────────────────────────────
-- TABLE users (super_admin, admin/directeur)
-- ─────────────────────────────────────────
CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    ecole_id INT,
    nom VARCHAR(100),
    email VARCHAR(100) UNIQUE,
    password VARCHAR(255),
    role ENUM('super_admin', 'admin') NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (ecole_id) REFERENCES ecoles(id) ON DELETE CASCADE
);

-- ─────────────────────────────────────────
-- TABLE enseignants (enseignant, secretaire)
-- Créés par l'admin(directeur) de l'école
-- ─────────────────────────────────────────
CREATE TABLE enseignants (
    id INT AUTO_INCREMENT PRIMARY KEY,
    ecole_id INT,
    nom VARCHAR(100) NOT NULL,
    prenom VARCHAR(100),
    email VARCHAR(100),
    tel VARCHAR(50),
    password VARCHAR(255),
    role ENUM('enseignant', 'secretaire') NOT NULL DEFAULT 'enseignant',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    UNIQUE KEY (email),
    UNIQUE KEY (tel),
    FOREIGN KEY (ecole_id) REFERENCES ecoles(id) ON DELETE CASCADE
);

-- ─────────────────────────────────────────
-- TABLE annees (année scolaire)
-- ─────────────────────────────────────────
CREATE TABLE annees (
    id INT AUTO_INCREMENT PRIMARY KEY,
    libelle VARCHAR(20) NOT NULL,
    etat INT NOT NULL DEFAULT 0,
    UNIQUE KEY (libelle)
);

-- ─────────────────────────────────────────
-- TABLE classes
-- ─────────────────────────────────────────
CREATE TABLE classes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    ecole_id INT,
    nom ENUM(
        'Toute Petite Section',
        'Petite Section',
        'Moyenne Section',
        'Grande Section',
        'CP1','CP2',
        'CE1','CE2',
        'CM1','CM2'
    ) NOT NULL,
    enseignant_id INT,
    FOREIGN KEY (ecole_id) REFERENCES ecoles(id) ON DELETE CASCADE,
    FOREIGN KEY (enseignant_id) REFERENCES enseignants(id) ON DELETE SET NULL
);

-- ─────────────────────────────────────────
-- TABLE eleves
-- ─────────────────────────────────────────
CREATE TABLE eleves (
    id INT AUTO_INCREMENT PRIMARY KEY,
    ecole_id INT,
    matricule VARCHAR(50) UNIQUE,
    nom VARCHAR(100) NOT NULL,
    prenom VARCHAR(100) NOT NULL,
    date_naissance DATE,
    lieu_naissance VARCHAR(150),
    domicile VARCHAR(255),
    tel_tuteur VARCHAR(50),
    nom_tuteur VARCHAR(100),
    nom_pere VARCHAR(100),
    nom_mere VARCHAR(100),
    tel_pere VARCHAR(50),
    tel_mere VARCHAR(50),
    profession_pere VARCHAR(100),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (ecole_id) REFERENCES ecoles(id) ON DELETE CASCADE
);

-- ─────────────────────────────────────────
-- TABLE inscriptions
-- ─────────────────────────────────────────
CREATE TABLE inscriptions (
    id INT AUTO_INCREMENT PRIMARY KEY,
    ecole_id INT,
    eleve_id INT NOT NULL,
    classe_id INT NOT NULL,
    annee_id INT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    UNIQUE KEY (eleve_id, classe_id, annee_id),
    FOREIGN KEY (ecole_id) REFERENCES ecoles(id) ON DELETE CASCADE,
    FOREIGN KEY (eleve_id) REFERENCES eleves(id) ON DELETE CASCADE,
    FOREIGN KEY (classe_id) REFERENCES classes(id) ON DELETE CASCADE,
    FOREIGN KEY (annee_id) REFERENCES annees(id) ON DELETE CASCADE
);

-- ─────────────────────────────────────────
-- TABLE compositions
-- ─────────────────────────────────────────
CREATE TABLE compositions (
    id INT AUTO_INCREMENT PRIMARY KEY,
    ecole_id INT NOT NULL,
    annee_id INT NOT NULL,
    libelle VARCHAR(50) NOT NULL,
    ordre INT NOT NULL,
    date_debut DATE NOT NULL,
    date_fin DATE NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (ecole_id) REFERENCES ecoles(id) ON DELETE CASCADE,
    FOREIGN KEY (annee_id) REFERENCES annees(id) ON DELETE CASCADE
);

-- ─────────────────────────────────────────
-- TABLE matieres
-- ─────────────────────────────────────────
CREATE TABLE matieres (
    id INT AUTO_INCREMENT PRIMARY KEY,
    ecole_id INT,
    nom VARCHAR(100) NOT NULL,
    coefficient INT DEFAULT 1,
    FOREIGN KEY (ecole_id) REFERENCES ecoles(id) ON DELETE CASCADE
);

-- ─────────────────────────────────────────
-- TABLE notes
-- ─────────────────────────────────────────
CREATE TABLE notes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    ecole_id INT,
    inscription_id INT NOT NULL,
    enseignant_id INT,
    matiere_id INT NOT NULL,
    composition_id INT NOT NULL,
    note DECIMAL(5,2),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    UNIQUE KEY (inscription_id, matiere_id, composition_id),
    FOREIGN KEY (ecole_id) REFERENCES ecoles(id) ON DELETE CASCADE,
    FOREIGN KEY (inscription_id) REFERENCES inscriptions(id) ON DELETE CASCADE,
    FOREIGN KEY (enseignant_id) REFERENCES enseignants(id) ON DELETE SET NULL,
    FOREIGN KEY (matiere_id) REFERENCES matieres(id) ON DELETE CASCADE,
    FOREIGN KEY (composition_id) REFERENCES compositions(id) ON DELETE CASCADE
);

-- ─────────────────────────────────────────
-- TABLE scolarites
-- (frais par classe et par année)
-- ─────────────────────────────────────────
CREATE TABLE scolarites (
    id INT AUTO_INCREMENT PRIMARY KEY,
    ecole_id INT NOT NULL,
    classe_id INT NOT NULL,
    annee_id INT NOT NULL,
    montant_inscription DECIMAL(10,2) NOT NULL DEFAULT 0,
    montant_mensuel DECIMAL(10,2) NOT NULL DEFAULT 0,
    montant_annuel DECIMAL(10,2) NOT NULL DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    UNIQUE KEY (ecole_id, classe_id, annee_id),
    FOREIGN KEY (ecole_id) REFERENCES ecoles(id) ON DELETE CASCADE,
    FOREIGN KEY (classe_id) REFERENCES classes(id) ON DELETE CASCADE,
    FOREIGN KEY (annee_id) REFERENCES annees(id) ON DELETE CASCADE
);

-- ─────────────────────────────────────────
-- TABLE paiements
-- ─────────────────────────────────────────
CREATE TABLE paiements (
    id INT AUTO_INCREMENT PRIMARY KEY,
    ecole_id INT NOT NULL,
    inscription_id INT NOT NULL,
    montant DECIMAL(10,2) NOT NULL,
    date_paiement DATETIME NOT NULL,
    statut ENUM('paye','partiel','impaye') NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (ecole_id) REFERENCES ecoles(id) ON DELETE CASCADE,
    FOREIGN KEY (inscription_id) REFERENCES inscriptions(id) ON DELETE CASCADE
);

-- ─────────────────────────────────────────
-- TABLE factures
-- (générée automatiquement après chaque paiement)
-- ─────────────────────────────────────────
CREATE TABLE factures (
    id INT AUTO_INCREMENT PRIMARY KEY,
    ecole_id INT NOT NULL,
    paiement_id INT NOT NULL,
    inscription_id INT NOT NULL,
    numero_facture VARCHAR(50) NOT NULL,
    nom_eleve VARCHAR(200) NOT NULL,
    classe_nom VARCHAR(100) NOT NULL,
    annee_libelle VARCHAR(20) NOT NULL,
    montant_scolarite DECIMAL(10,2) NOT NULL,
    montant_paye DECIMAL(10,2) NOT NULL,
    montant_reste DECIMAL(10,2) NOT NULL,
    date_paiement DATETIME NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    UNIQUE KEY (numero_facture),
    FOREIGN KEY (ecole_id) REFERENCES ecoles(id) ON DELETE CASCADE,
    FOREIGN KEY (paiement_id) REFERENCES paiements(id) ON DELETE CASCADE,
    FOREIGN KEY (inscription_id) REFERENCES inscriptions(id) ON DELETE CASCADE
);