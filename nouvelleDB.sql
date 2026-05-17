-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Hôte : 127.0.0.1:3306
-- Généré le : sam. 16 mai 2026 à 22:22
-- Version du serveur : 9.1.0
-- Version de PHP : 8.3.14

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de données : `gestionecole`
--

-- --------------------------------------------------------

--
-- Structure de la table `annees`
--

DROP TABLE IF EXISTS `annees`;
CREATE TABLE IF NOT EXISTS `annees` (
  `id` int NOT NULL AUTO_INCREMENT,
  `libelle` varchar(20) NOT NULL,
  `etat` int NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  UNIQUE KEY `libelle` (`libelle`)
) ENGINE=MyISAM AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Déchargement des données de la table `annees`
--

INSERT INTO `annees` (`id`, `libelle`, `etat`) VALUES
(1, '2024-2025', 0),
(2, '2025-2026', 1);

-- --------------------------------------------------------

--
-- Structure de la table `classes`
--

DROP TABLE IF EXISTS `classes`;
CREATE TABLE IF NOT EXISTS `classes` (
  `id` int NOT NULL AUTO_INCREMENT,
  `ecole_id` int DEFAULT NULL,
  `nom` enum('Toute Petite Section','Petite Section','Moyenne Section','Grande Section','CP1','CP2','CE1','CE2','CM1','CM2') NOT NULL,
  `enseignant_id` int DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `ecole_id` (`ecole_id`),
  KEY `enseignant_id` (`enseignant_id`)
) ENGINE=MyISAM AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Déchargement des données de la table `classes`
--

INSERT INTO `classes` (`id`, `ecole_id`, `nom`, `enseignant_id`) VALUES
(1, 1, 'CP1', 4),
(2, 2, 'CP1', 0),
(3, 1, 'CM2', 3),
(4, 1, 'CM1', 4);

-- --------------------------------------------------------

--
-- Structure de la table `compositions`
--

DROP TABLE IF EXISTS `compositions`;
CREATE TABLE IF NOT EXISTS `compositions` (
  `id` int NOT NULL AUTO_INCREMENT,
  `ecole_id` int NOT NULL,
  `annee_id` int NOT NULL,
  `libelle` varchar(50) NOT NULL,
  `ordre` int NOT NULL,
  `date_debut` date NOT NULL,
  `date_fin` date NOT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `ecole_id` (`ecole_id`),
  KEY `annee_id` (`annee_id`)
) ENGINE=MyISAM AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Déchargement des données de la table `compositions`
--

INSERT INTO `compositions` (`id`, `ecole_id`, `annee_id`, `libelle`, `ordre`, `date_debut`, `date_fin`, `created_at`) VALUES
(1, 1, 2, 'composition du 12 fevrier 2025- 2026', 1, '2026-05-16', '2026-05-17', '2026-05-15 23:52:50'),
(2, 1, 2, 'Composition 2', 2, '2026-05-29', '2026-05-30', '2026-05-16 12:43:21'),
(3, 1, 2, 'composition de passage', 3, '2026-06-07', '2026-06-10', '2026-05-16 17:56:37');

-- --------------------------------------------------------

--
-- Structure de la table `ecoles`
--

DROP TABLE IF EXISTS `ecoles`;
CREATE TABLE IF NOT EXISTS `ecoles` (
  `id` int NOT NULL AUTO_INCREMENT,
  `nom` varchar(150) NOT NULL,
  `adresse` varchar(255) DEFAULT NULL,
  `telephone` varchar(50) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `date_creation` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Déchargement des données de la table `ecoles`
--

INSERT INTO `ecoles` (`id`, `nom`, `adresse`, `telephone`, `email`, `date_creation`) VALUES
(1, 'chrotcho', 'Bouake, Cote d\'Ivoire', '0504311985', 'Issouf23.dev@gmail.com', '2026-05-03 21:59:46'),
(2, 'Soro', 'Bouake, Cote d\'Ivoire', '0504311985', 'href@gmail.com', '2026-05-07 23:44:01');

-- --------------------------------------------------------

--
-- Structure de la table `eleves`
--

DROP TABLE IF EXISTS `eleves`;
CREATE TABLE IF NOT EXISTS `eleves` (
  `id` int NOT NULL AUTO_INCREMENT,
  `ecole_id` int DEFAULT NULL,
  `matricule` varchar(50) DEFAULT NULL,
  `nom` varchar(100) NOT NULL,
  `prenom` varchar(100) NOT NULL,
  `date_naissance` date DEFAULT NULL,
  `lieu_naissance` varchar(150) DEFAULT NULL,
  `domicile` varchar(255) DEFAULT NULL,
  `tel_tuteur` varchar(50) DEFAULT NULL,
  `nom_tuteur` varchar(100) DEFAULT NULL,
  `nom_pere` varchar(100) DEFAULT NULL,
  `nom_mere` varchar(100) DEFAULT NULL,
  `tel_pere` varchar(50) DEFAULT NULL,
  `tel_mere` varchar(50) DEFAULT NULL,
  `profession_pere` varchar(100) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `matricule` (`matricule`),
  KEY `ecole_id` (`ecole_id`)
) ENGINE=MyISAM AUTO_INCREMENT=10 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Déchargement des données de la table `eleves`
--

INSERT INTO `eleves` (`id`, `ecole_id`, `matricule`, `nom`, `prenom`, `date_naissance`, `lieu_naissance`, `domicile`, `tel_tuteur`, `nom_tuteur`, `nom_pere`, `nom_mere`, `tel_pere`, `tel_mere`, `profession_pere`, `created_at`) VALUES
(1, 1, '16355976R', 'Soro', 'Issouf', '2026-05-27', 'kougbere', 'bke', '244254556', 'Issouf Soro', 'Issouf Soro', 'lomm', '0718438732', '0718438111', 'CULTIVATEUR', '2026-05-03 22:18:15'),
(2, 1, '1R', 'Soro', 'Issouf', '2026-05-21', 'kougbere', 'bke', '0718438736', 'Issouf Soro', 'Issouf Soro', 'Issouf Soro', '0718438732', '0718438739', 'CULTIVATEUR', '2026-05-09 15:02:42'),
(3, 1, 'BGV', 'Soro', 'Issouf', '2026-04-30', 'kougbere', 'bke', '0718438736', 'BB', 'Issouf Soro', 'Issouf Soro', '', '0718438111', 'CULTIVATEUR', '2026-05-12 21:04:29'),
(4, 1, '1A', 'solo', 'badra', '2026-04-29', 'bke', 'bke', '0718430736', 'Issouf Soro', 'yeo', 'yoh', '0718488002', '0710038111', 'CULTIVATEUR', '2026-05-13 00:41:53'),
(5, 1, '1B', 'halil', 'bop', '2026-05-07', 'botro', 'bke', '0718000736', 'Issouf Soro', 'bop dilane', 'Issouf Soro', '0700488002', '0710038119', 'astronaut', '2026-05-13 12:17:02'),
(6, 1, 'C1', 'boubo', 'Issouf', '2026-05-07', 'seek', 'bke', '0718000000', 'Issouf Soro', 'Issouf Soro', 'Issouf', '07184900002', '071000739', 'CULTIVATEUR', '2026-05-16 10:11:25'),
(7, 1, '1C', 'b', 'dd', '2026-05-28', 'bke', 'bke', '071843865', 'Issouf Soro', 'Issouf Soro', 'Issouf Soro', '071843890', '0787038111', 'CULTIVATEUR', '2026-05-16 17:49:48'),
(8, 1, '1D', 'tt', 'iu', '2026-05-27', 'seek', 'bke', '0718000736', 'Issouf Soro', 'Issouf Soro', 'jtdfyk', '0718438002', '0700000000', 'CULTIVATEUR', '2026-05-16 17:51:34'),
(9, 1, '1E', 'Soro', 'Issouf', '2026-05-26', 'bke', 'bke', '071800000', 'Issouf Soro', 'Issouf Soro', 'Issouf Soro', '0718488002', '122323434', 'astronaut', '2026-05-16 17:53:52');

-- --------------------------------------------------------

--
-- Structure de la table `enseignants`
--

DROP TABLE IF EXISTS `enseignants`;
CREATE TABLE IF NOT EXISTS `enseignants` (
  `id` int NOT NULL AUTO_INCREMENT,
  `ecole_id` int DEFAULT NULL,
  `nom` varchar(100) NOT NULL,
  `prenom` varchar(100) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `tel` varchar(50) DEFAULT NULL,
  `password` varchar(255) DEFAULT NULL,
  `role` enum('enseignant','secretaire') NOT NULL DEFAULT 'enseignant',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `email` (`email`),
  UNIQUE KEY `tel` (`tel`),
  KEY `ecole_id` (`ecole_id`)
) ENGINE=MyISAM AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Déchargement des données de la table `enseignants`
--

INSERT INTO `enseignants` (`id`, `ecole_id`, `nom`, `prenom`, `email`, `tel`, `password`, `role`, `created_at`) VALUES
(1, 2, 'soui', 'issouf', 'd@gmail.com', '0504311985', '$2y$10$BfAGim5EYBHS1uT33PFor.65JAydo.A/Jc8/2ii04fJdGrIXPE85C', 'enseignant', '2026-05-08 15:00:39'),
(2, 2, 'bobo', 'bob', 'bobo@gmail.com', '0504319099', '$2y$10$OpGY4AnaojMCqgSBP4IOVepY2PkJb8NPu07K0I5BhYHJFcdMzcEe6', 'enseignant', '2026-05-08 15:43:33'),
(3, 1, 'doro', 'Issouf', 'doro@gmail.com', '050000985', '$2y$10$791tgj6SqepW7wxM0FNnEeS1L/8icXN4.W8mQdah/gmhJlOrVwFj2', 'enseignant', '2026-05-13 12:20:05'),
(4, 1, 'walki', 'wak', 'wak@gmail.com', '0584311985', '$2y$10$V8e6j3UBYoM9.giC4HjCH.adSAE3qn9mc442BJ3d24/lA7SyslqhW', 'enseignant', '2026-05-14 10:40:01');

-- --------------------------------------------------------

--
-- Structure de la table `factures`
--

DROP TABLE IF EXISTS `factures`;
CREATE TABLE IF NOT EXISTS `factures` (
  `id` int NOT NULL AUTO_INCREMENT,
  `ecole_id` int NOT NULL,
  `paiement_id` int NOT NULL,
  `inscription_id` int NOT NULL,
  `numero_facture` varchar(50) NOT NULL,
  `nom_eleve` varchar(200) NOT NULL,
  `classe_nom` varchar(100) NOT NULL,
  `annee_libelle` varchar(20) NOT NULL,
  `montant_scolarite` decimal(10,2) NOT NULL,
  `montant_paye` decimal(10,2) NOT NULL,
  `montant_reste` decimal(10,2) NOT NULL,
  `date_paiement` datetime NOT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `numero_facture` (`numero_facture`),
  KEY `ecole_id` (`ecole_id`),
  KEY `paiement_id` (`paiement_id`),
  KEY `inscription_id` (`inscription_id`)
) ENGINE=MyISAM AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Déchargement des données de la table `factures`
--

INSERT INTO `factures` (`id`, `ecole_id`, `paiement_id`, `inscription_id`, `numero_facture`, `nom_eleve`, `classe_nom`, `annee_libelle`, `montant_scolarite`, `montant_paye`, `montant_reste`, `date_paiement`, `created_at`) VALUES
(1, 1, 1, 2, 'FAC-1-2025-0001', 'Soro Issouf', 'CP1', '2025-2026', 0.00, 4200.00, 0.00, '2026-05-09 15:01:00', '2026-05-09 15:02:42'),
(2, 1, 2, 3, 'FAC-1-2025-0002', 'Soro Issouf', 'CM2', '2025-2026', 0.00, 4000.00, 0.00, '2026-05-12 21:03:00', '2026-05-12 21:04:29'),
(3, 1, 3, 4, 'FAC-1-2025-0003', 'solo badra', 'CP1', '2025-2026', 0.00, 100.00, 0.00, '2026-05-13 00:41:00', '2026-05-13 00:41:53'),
(4, 1, 4, 5, 'FAC-1-2025-0004', 'halil bop', 'CM2', '2025-2026', 0.00, 100.00, 0.00, '2026-05-13 12:16:00', '2026-05-13 12:17:02'),
(5, 1, 5, 6, 'FAC-1-2025-0005', 'boubo Issouf', 'CM1', '2025-2026', 100000.00, 100000.00, 0.00, '2026-05-16 10:11:00', '2026-05-16 10:11:25'),
(6, 1, 6, 7, 'FAC-1-2025-0006', 'b dd', 'CM2', '2025-2026', 120000.00, 120000.00, 0.00, '2026-05-16 17:49:00', '2026-05-16 17:49:48'),
(7, 1, 7, 8, 'FAC-1-2025-0007', 'tt iu', 'CM2', '2025-2026', 120000.00, 120000.00, 0.00, '2026-05-16 17:51:00', '2026-05-16 17:51:34'),
(8, 1, 8, 9, 'FAC-1-2025-0008', 'Soro Issouf', 'CM2', '2025-2026', 120000.00, 120000.00, 0.00, '2026-05-16 17:53:00', '2026-05-16 17:53:52');

-- --------------------------------------------------------

--
-- Structure de la table `inscriptions`
--

DROP TABLE IF EXISTS `inscriptions`;
CREATE TABLE IF NOT EXISTS `inscriptions` (
  `id` int NOT NULL AUTO_INCREMENT,
  `ecole_id` int DEFAULT NULL,
  `eleve_id` int NOT NULL,
  `classe_id` int NOT NULL,
  `annee_id` int NOT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `eleve_id` (`eleve_id`,`classe_id`,`annee_id`),
  KEY `ecole_id` (`ecole_id`),
  KEY `classe_id` (`classe_id`),
  KEY `annee_id` (`annee_id`)
) ENGINE=MyISAM AUTO_INCREMENT=10 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Déchargement des données de la table `inscriptions`
--

INSERT INTO `inscriptions` (`id`, `ecole_id`, `eleve_id`, `classe_id`, `annee_id`, `created_at`) VALUES
(1, NULL, 1, 1, 1, '2026-05-04 22:15:14'),
(2, 1, 2, 1, 2, '2026-05-09 15:02:42'),
(3, 1, 3, 3, 2, '2026-05-12 21:04:29'),
(4, 1, 4, 1, 2, '2026-05-13 00:41:53'),
(5, 1, 5, 3, 2, '2026-05-13 12:17:02'),
(6, 1, 6, 4, 2, '2026-05-16 10:11:25'),
(7, 1, 7, 3, 2, '2026-05-16 17:49:48'),
(8, 1, 8, 3, 2, '2026-05-16 17:51:34'),
(9, 1, 9, 3, 2, '2026-05-16 17:53:52');

-- --------------------------------------------------------

--
-- Structure de la table `matieres`
--

DROP TABLE IF EXISTS `matieres`;
CREATE TABLE IF NOT EXISTS `matieres` (
  `id` int NOT NULL AUTO_INCREMENT,
  `ecole_id` int DEFAULT NULL,
  `classe_id` int DEFAULT NULL,
  `nom` varchar(100) NOT NULL,
  `note_sur` decimal(5,2) NOT NULL DEFAULT '50.00',
  PRIMARY KEY (`id`),
  KEY `ecole_id` (`ecole_id`),
  KEY `classe_id` (`classe_id`)
) ENGINE=MyISAM AUTO_INCREMENT=32 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Déchargement des données de la table `matieres`
--

INSERT INTO `matieres` (`id`, `ecole_id`, `classe_id`, `nom`, `note_sur`) VALUES
(1, NULL, NULL, 'Eveil au milieu', 50.00),
(2, NULL, NULL, 'Soro', 50.00),
(21, 1, 4, 'exploitation de texte', 50.00),
(22, 1, 4, 'mathematique', 50.00),
(23, 1, 4, 'dictee', 20.00),
(20, 1, 4, 'eveil au milieu', 50.00),
(15, 1, 1, 'lecture/ecriture', 10.00),
(17, 1, 1, 'EDHC', 10.00),
(18, 1, 1, 'mathematique', 10.00),
(19, 1, 1, 'Dessin', 10.00),
(30, 1, 3, 'mathematique', 50.00),
(28, 1, 3, 'eveil au milieu', 50.00),
(29, 1, 3, 'exploitation de texte', 50.00),
(31, 1, 3, 'dictee', 20.00);

-- --------------------------------------------------------

--
-- Structure de la table `notes`
--

DROP TABLE IF EXISTS `notes`;
CREATE TABLE IF NOT EXISTS `notes` (
  `id` int NOT NULL AUTO_INCREMENT,
  `ecole_id` int DEFAULT NULL,
  `inscription_id` int NOT NULL,
  `enseignant_id` int DEFAULT NULL,
  `matiere_id` int NOT NULL,
  `composition_id` int NOT NULL,
  `note` decimal(5,2) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `inscription_id` (`inscription_id`,`matiere_id`,`composition_id`),
  KEY `ecole_id` (`ecole_id`),
  KEY `enseignant_id` (`enseignant_id`),
  KEY `matiere_id` (`matiere_id`),
  KEY `composition_id` (`composition_id`)
) ENGINE=MyISAM AUTO_INCREMENT=45 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Déchargement des données de la table `notes`
--

INSERT INTO `notes` (`id`, `ecole_id`, `inscription_id`, `enseignant_id`, `matiere_id`, `composition_id`, `note`, `created_at`) VALUES
(1, 1, 4, 3, 3, 1, 7.25, '2026-05-16 00:08:35'),
(2, 1, 4, 3, 6, 1, 2.75, '2026-05-16 00:08:35'),
(3, 1, 4, 3, 4, 1, 3.25, '2026-05-16 00:08:35'),
(4, 1, 4, 3, 5, 1, 3.75, '2026-05-16 00:08:35'),
(5, 1, 2, 3, 3, 1, 20.00, '2026-05-16 00:08:35'),
(6, 1, 2, 3, 6, 1, 15.00, '2026-05-16 00:08:35'),
(7, 1, 2, 3, 4, 1, 12.00, '2026-05-16 00:08:35'),
(8, 1, 2, 3, 5, 1, 19.00, '2026-05-16 00:08:35'),
(9, 1, 4, 3, 19, 1, 5.00, '2026-05-16 11:22:27'),
(10, 1, 4, 3, 17, 1, 6.00, '2026-05-16 11:22:27'),
(11, 1, 4, 3, 15, 1, 8.00, '2026-05-16 11:22:27'),
(12, 1, 4, 3, 18, 1, 2.00, '2026-05-16 11:22:27'),
(13, 1, 2, 3, 19, 1, 10.00, '2026-05-16 11:22:27'),
(14, 1, 2, 3, 17, 1, 8.00, '2026-05-16 11:22:27'),
(15, 1, 2, 3, 15, 1, 6.00, '2026-05-16 11:22:27'),
(16, 1, 2, 3, 18, 1, 9.00, '2026-05-16 11:22:27'),
(17, 1, 6, 3, 23, 1, 12.00, '2026-05-16 12:02:23'),
(18, 1, 6, 3, 20, 1, 25.00, '2026-05-16 12:02:23'),
(19, 1, 6, 3, 21, 1, 49.00, '2026-05-16 12:02:23'),
(20, 1, 6, 3, 22, 1, 45.00, '2026-05-16 12:02:23'),
(21, 1, 6, 3, 23, 2, 18.00, '2026-05-16 12:45:33'),
(22, 1, 6, 3, 20, 2, 40.00, '2026-05-16 12:45:33'),
(23, 1, 6, 3, 21, 2, 40.00, '2026-05-16 12:45:33'),
(24, 1, 6, 3, 22, 2, 12.00, '2026-05-16 12:45:33'),
(25, 1, 7, 3, 31, 3, 18.00, '2026-05-16 18:07:32'),
(26, 1, 7, 3, 28, 3, 50.00, '2026-05-16 18:07:32'),
(27, 1, 7, 3, 29, 3, 33.00, '2026-05-16 18:07:32'),
(28, 1, 7, 3, 30, 3, 43.00, '2026-05-16 18:07:32'),
(29, 1, 5, 3, 31, 3, 3.00, '2026-05-16 18:07:32'),
(30, 1, 5, 3, 28, 3, 10.00, '2026-05-16 18:07:32'),
(31, 1, 5, 3, 29, 3, 28.00, '2026-05-16 18:07:32'),
(32, 1, 5, 3, 30, 3, 50.00, '2026-05-16 18:07:32'),
(33, 1, 3, 3, 31, 3, 19.00, '2026-05-16 18:07:32'),
(34, 1, 3, 3, 28, 3, 45.00, '2026-05-16 18:07:32'),
(35, 1, 3, 3, 29, 3, 33.00, '2026-05-16 18:07:32'),
(36, 1, 3, 3, 30, 3, 50.00, '2026-05-16 18:07:32'),
(37, 1, 9, 3, 31, 3, 2.00, '2026-05-16 18:07:32'),
(38, 1, 9, 3, 28, 3, 12.00, '2026-05-16 18:07:32'),
(39, 1, 9, 3, 29, 3, 34.00, '2026-05-16 18:07:32'),
(40, 1, 9, 3, 30, 3, 22.00, '2026-05-16 18:07:32'),
(41, 1, 8, 3, 31, 3, 15.00, '2026-05-16 18:07:32'),
(42, 1, 8, 3, 28, 3, 13.00, '2026-05-16 18:07:32'),
(43, 1, 8, 3, 29, 3, 12.00, '2026-05-16 18:07:32'),
(44, 1, 8, 3, 30, 3, 12.00, '2026-05-16 18:07:32');

-- --------------------------------------------------------

--
-- Structure de la table `paiements`
--

DROP TABLE IF EXISTS `paiements`;
CREATE TABLE IF NOT EXISTS `paiements` (
  `id` int NOT NULL AUTO_INCREMENT,
  `ecole_id` int NOT NULL,
  `inscription_id` int NOT NULL,
  `montant` decimal(10,2) NOT NULL,
  `date_paiement` datetime NOT NULL,
  `statut` enum('paye','partiel','impaye') NOT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `ecole_id` (`ecole_id`),
  KEY `inscription_id` (`inscription_id`)
) ENGINE=MyISAM AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Déchargement des données de la table `paiements`
--

INSERT INTO `paiements` (`id`, `ecole_id`, `inscription_id`, `montant`, `date_paiement`, `statut`, `created_at`) VALUES
(1, 1, 2, 4200.00, '2026-05-09 15:01:00', 'paye', '2026-05-09 15:02:42'),
(2, 1, 3, 4000.00, '2026-05-12 21:03:00', 'paye', '2026-05-12 21:04:29'),
(3, 1, 4, 100.00, '2026-05-13 00:41:00', 'paye', '2026-05-13 00:41:53'),
(4, 1, 5, 100.00, '2026-05-13 12:16:00', 'partiel', '2026-05-13 12:17:02'),
(5, 1, 6, 100000.00, '2026-05-16 10:11:00', 'paye', '2026-05-16 10:11:25'),
(6, 1, 7, 120000.00, '2026-05-16 17:49:00', 'paye', '2026-05-16 17:49:48'),
(7, 1, 8, 120000.00, '2026-05-16 17:51:00', 'paye', '2026-05-16 17:51:34'),
(8, 1, 9, 120000.00, '2026-05-16 17:53:00', 'paye', '2026-05-16 17:53:52');

-- --------------------------------------------------------

--
-- Structure de la table `scolarites`
--

DROP TABLE IF EXISTS `scolarites`;
CREATE TABLE IF NOT EXISTS `scolarites` (
  `id` int NOT NULL AUTO_INCREMENT,
  `ecole_id` int NOT NULL,
  `classe_id` int NOT NULL,
  `annee_id` int NOT NULL,
  `montant_inscription` decimal(10,2) NOT NULL DEFAULT '0.00',
  `montant_mensuel` decimal(10,2) NOT NULL DEFAULT '0.00',
  `montant_annuel` decimal(10,2) NOT NULL DEFAULT '0.00',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `ecole_id` (`ecole_id`,`classe_id`,`annee_id`),
  KEY `classe_id` (`classe_id`),
  KEY `annee_id` (`annee_id`)
) ENGINE=MyISAM AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Déchargement des données de la table `scolarites`
--

INSERT INTO `scolarites` (`id`, `ecole_id`, `classe_id`, `annee_id`, `montant_inscription`, `montant_mensuel`, `montant_annuel`, `created_at`) VALUES
(1, 1, 1, 2, 2000.00, 500.00, 2000.00, '2026-05-14 11:20:05'),
(2, 1, 4, 2, 100000.00, 25000.00, 100000.00, '2026-05-14 11:28:12'),
(3, 1, 3, 2, 120000.00, 30000.00, 120000.00, '2026-05-14 11:28:53');

-- --------------------------------------------------------

--
-- Structure de la table `users`
--

DROP TABLE IF EXISTS `users`;
CREATE TABLE IF NOT EXISTS `users` (
  `id` int NOT NULL AUTO_INCREMENT,
  `ecole_id` int DEFAULT NULL,
  `nom` varchar(100) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `password` varchar(255) DEFAULT NULL,
  `role` enum('super_admin','admin') NOT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `email` (`email`),
  KEY `ecole_id` (`ecole_id`)
) ENGINE=MyISAM AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Déchargement des données de la table `users`
--

INSERT INTO `users` (`id`, `ecole_id`, `nom`, `email`, `password`, `role`, `created_at`) VALUES
(1, NULL, 'zie', 'zie@gmail.com', '$2y$10$OVg0bM7zT0S/vJ/Dtx1u5ei87HIzrlZffvgI1dGq29ub4hyipGXay', 'super_admin', '2026-05-03 21:57:54'),
(3, 1, 'chrotcho', 'chrotcho@gmail.com', '$2y$10$3DC9m2oxPck8mrcAIVIQQOm835zYxzFXFICvzX.dLqPVdLIkK283y', 'admin', '2026-05-03 22:08:19'),
(4, 2, 'sylla', 'href@gmail.com', '$2y$10$qSfkch5tJDFP8c4OSuuOVOQGYsRAlAtSVj/fpEzdozXXtJbgxxDdC', 'admin', '2026-05-07 23:47:21');
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
