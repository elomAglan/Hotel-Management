-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Hôte : 127.0.0.1:3306
-- Généré le : mar. 24 sep. 2024 à 17:59
-- Version du serveur : 8.3.0
-- Version de PHP : 8.2.18

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de données : `hotel_management`
--

-- --------------------------------------------------------

--
-- Structure de la table `chambres`
--

DROP TABLE IF EXISTS `chambres`;
CREATE TABLE IF NOT EXISTS `chambres` (
  `id` int NOT NULL AUTO_INCREMENT,
  `numero_chambre` varchar(50) NOT NULL,
  `type_chambre` varchar(50) NOT NULL,
  `prix` decimal(10,2) NOT NULL,
  `statut` enum('Libre','Occupé','En maintenance') DEFAULT 'Libre',
  `date_heure_occupation` datetime DEFAULT NULL,
  `image_url` varchar(255) DEFAULT NULL,
  `description` text,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=97 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Déchargement des données de la table `chambres`
--

INSERT INTO `chambres` (`id`, `numero_chambre`, `type_chambre`, `prix`, `statut`, `date_heure_occupation`, `image_url`, `description`) VALUES
(96, '303', 'Suite', 250.00, 'Libre', NULL, 'images/h4.jpg', 'Suite présidentielle avec chambre à coucher et salle à manger séparées.'),
(95, '302', 'Suite', 220.00, 'Libre', NULL, 'images/h5.jpg', 'Suite avec jacuzzi et terrasse privée.'),
(94, '301', 'Suite', 200.00, 'Occupé', NULL, 'images/h6.jpg', 'Suite luxueuse avec salon séparé et vue imprenable.'),
(93, '203', 'Deluxe', 140.00, 'Libre', NULL, 'images/hh.jpg', 'Chambre Deluxe avec balcon privé et vue sur le parc.'),
(91, '201', 'Deluxe', 120.00, 'Libre', NULL, 'images/h3.jpg', 'Chambre Deluxe avec lit king size et baignoire spa.'),
(92, '202', 'Deluxe', 130.00, 'Libre', NULL, 'images/h11.jpg', 'Chambre Deluxe avec vue panoramique sur la mer.'),
(89, '102', 'Standard', 85.00, 'Libre', NULL, 'images/h8.jpg', 'Chambre Standard avec deux lits simples et salle de bains privative.'),
(90, '103', 'Standard', 90.00, 'Libre', NULL, 'images/h7.jpg', 'Chambre Standard avec grand lit et vue sur la ville.'),
(88, '101', 'Standard', 80.00, 'Libre', NULL, 'images/h9.jpg', 'Chambre Standard avec lit double et vue sur le jardin.');

-- --------------------------------------------------------

--
-- Structure de la table `clients`
--

DROP TABLE IF EXISTS `clients`;
CREATE TABLE IF NOT EXISTS `clients` (
  `id` int NOT NULL AUTO_INCREMENT,
  `nom` varchar(100) NOT NULL,
  `prenom` varchar(100) NOT NULL,
  `num_carte` varchar(20) DEFAULT NULL,
  `telephone` varchar(20) NOT NULL,
  `adresse` text NOT NULL,
  `date_creation` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=20 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Déchargement des données de la table `clients`
--

INSERT INTO `clients` (`id`, `nom`, `prenom`, `num_carte`, `telephone`, `adresse`, `date_creation`) VALUES
(16, 'atikpo', 'komi komla', '4241245255214', '98801667', 'Tabligbo', '2024-08-19 14:28:10'),
(17, 'AGLAN', 'elom', '4241245255214', '98801667', 'lome', '2024-08-19 15:03:13'),
(18, 'Elom', 'AGLAN Yao Elom', '4241245255214', '98801667', 'TOKOIN', '2024-08-21 15:20:38');

-- --------------------------------------------------------

--
-- Structure de la table `paiements`
--

DROP TABLE IF EXISTS `paiements`;
CREATE TABLE IF NOT EXISTS `paiements` (
  `id` int NOT NULL AUTO_INCREMENT,
  `reservation_id` int NOT NULL,
  `client_id` int NOT NULL,
  `montant` decimal(10,2) NOT NULL,
  `mode_paiement` varchar(50) NOT NULL,
  `commentaire` text,
  `date_paiement` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `reservation_id` (`reservation_id`),
  KEY `client_id` (`client_id`)
) ENGINE=MyISAM AUTO_INCREMENT=52 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Déchargement des données de la table `paiements`
--

INSERT INTO `paiements` (`id`, `reservation_id`, `client_id`, `montant`, `mode_paiement`, `commentaire`, `date_paiement`) VALUES
(51, 1, 16, 250.00, 'cash', '', '2024-08-30 11:48:51'),
(50, 4, 16, 260.00, 'carte_credit', '', '2024-08-29 18:30:19');

-- --------------------------------------------------------

--
-- Structure de la table `reservations`
--

DROP TABLE IF EXISTS `reservations`;
CREATE TABLE IF NOT EXISTS `reservations` (
  `id` int NOT NULL AUTO_INCREMENT,
  `id_utilisateur` int NOT NULL,
  `id_chambre` int NOT NULL,
  `date_debut` date NOT NULL,
  `date_fin` date NOT NULL,
  `statut` enum('confirmée','annulée') DEFAULT 'confirmée',
  `client_id` int NOT NULL,
  `heure_arrivee` time DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `client_id` (`client_id`),
  KEY `id_chambre` (`id_chambre`),
  KEY `date_debut` (`date_debut`),
  KEY `date_fin` (`date_fin`)
) ENGINE=MyISAM AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Déchargement des données de la table `reservations`
--

INSERT INTO `reservations` (`id`, `id_utilisateur`, `id_chambre`, `date_debut`, `date_fin`, `statut`, `client_id`, `heure_arrivee`, `created_at`, `updated_at`) VALUES
(6, 0, 94, '2024-09-04', '2024-09-05', 'confirmée', 18, '00:28:00', '2024-09-04 00:28:52', '2024-09-04 00:28:52'),
(5, 0, 94, '2024-08-28', '2024-08-29', 'confirmée', 17, '18:50:00', '2024-08-29 18:50:34', '2024-08-29 18:50:34');

-- --------------------------------------------------------

--
-- Structure de la table `reservation_utilisateur`
--

DROP TABLE IF EXISTS `reservation_utilisateur`;
CREATE TABLE IF NOT EXISTS `reservation_utilisateur` (
  `id` int NOT NULL AUTO_INCREMENT,
  `nom` varchar(255) DEFAULT NULL,
  `prenom` varchar(255) DEFAULT NULL,
  `carte_identite` varchar(255) DEFAULT NULL,
  `date_arrivee` date DEFAULT NULL,
  `date_depart` date DEFAULT NULL,
  `chambre_id` int DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=38 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Structure de la table `reçu_de_paiement`
--

DROP TABLE IF EXISTS `reçu_de_paiement`;
CREATE TABLE IF NOT EXISTS `reçu_de_paiement` (
  `id` int NOT NULL AUTO_INCREMENT,
  `client_id` int NOT NULL,
  `montant` decimal(10,2) NOT NULL,
  `date_paiement` datetime NOT NULL,
  `mode_paiement` varchar(50) NOT NULL,
  `caissier_id` int NOT NULL,
  `commentaire` text,
  `date_creation` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `client_id` (`client_id`),
  KEY `caissier_id` (`caissier_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Structure de la table `utilisateurs`
--

DROP TABLE IF EXISTS `utilisateurs`;
CREATE TABLE IF NOT EXISTS `utilisateurs` (
  `id` int NOT NULL AUTO_INCREMENT,
  `username` varchar(50) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `role` enum('admin','user') NOT NULL DEFAULT 'user',
  PRIMARY KEY (`id`),
  UNIQUE KEY `username` (`username`),
  UNIQUE KEY `email` (`email`)
) ENGINE=MyISAM AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Déchargement des données de la table `utilisateurs`
--

INSERT INTO `utilisateurs` (`id`, `username`, `email`, `password`, `created_at`, `role`) VALUES
(1, 'bro', 'bro@gmail.com', '$2y$10$kUq7Jlt86x4yEiu/hOYsa.CPRfRV36wVB630aGx2IL/JRYiIMtvpi', '2024-08-12 13:35:45', 'admin'),
(2, 'Orguezy', 'blinklintelobp@gmail.com', '$2y$10$sXf6lSrZsuKu4BIISS/BveJE6o6WoJPuPsDVYn5z5TQvwF6VBeP/K', '2024-08-12 13:50:32', 'user');
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
