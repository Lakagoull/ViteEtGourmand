-- phpMyAdmin SQL Dump
-- version 4.9.0.1
-- https://www.phpmyadmin.net/
--
-- Hôte : sql108.infinityfree.com
-- Généré le :  jeu. 21 mai 2026 à 09:13
-- Version du serveur :  11.4.10-MariaDB
-- Version de PHP :  7.2.22

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de données :  `if0_41964657_base_viteetgourmand`
--

-- --------------------------------------------------------

--
-- Structure de la table `avis`
--

CREATE TABLE `avis` (
  `id` int(11) NOT NULL,
  `commande_id` int(11) NOT NULL,
  `nom_client` varchar(100) NOT NULL,
  `note` int(1) NOT NULL,
  `commentaire` text NOT NULL,
  `valide` tinyint(1) NOT NULL DEFAULT 0,
  `date_avis` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Déchargement des données de la table `avis`
--

INSERT INTO `avis` (`id`, `commande_id`, `nom_client`, `note`, `commentaire`, `valide`, `date_avis`) VALUES
(1, 0, 'Zineb.C', 5, 'Super bon', 1, '2026-05-20 22:13:51'),
(2, 0, 'Zineb.C', 5, 'Good', 1, '2026-05-20 22:26:06'),
(3, 0, 'Zineb.C', 5, 'Genial', 1, '2026-05-20 22:26:15');

-- --------------------------------------------------------

--
-- Structure de la table `historique_statuts`
--

CREATE TABLE `historique_statuts` (
  `id` int(11) NOT NULL,
  `id_reservation` int(11) NOT NULL,
  `statut` varchar(100) NOT NULL,
  `date_modification` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `historique_statuts`
--

INSERT INTO `historique_statuts` (`id`, `id_reservation`, `statut`, `date_modification`) VALUES
(21, 16, 'En attente', '2026-05-20 15:11:39'),
(22, 16, 'Payé via Carte Bancaire', '2026-05-20 15:12:09'),
(23, 16, 'En préparation', '2026-05-20 15:12:35'),
(24, 16, 'En cours de livraison', '2026-05-20 15:13:04'),
(25, 16, 'Livré', '2026-05-20 15:13:07'),
(26, 16, 'En attente retour matériel', '2026-05-20 15:13:10'),
(27, 16, 'Terminée', '2026-05-20 15:13:14'),
(28, 17, 'En attente', '2026-05-20 15:18:45'),
(29, 18, 'En attente', '2026-05-20 15:19:09'),
(30, 17, 'Payé via Carte Bancaire', '2026-05-20 15:19:27'),
(31, 18, 'Payé via PayPal', '2026-05-20 15:19:47'),
(32, 17, 'Terminée', '2026-05-20 15:20:20'),
(33, 18, 'Terminée', '2026-05-20 15:20:23');

-- --------------------------------------------------------

--
-- Structure de la table `menus`
--

CREATE TABLE `menus` (
  `id` int(11) NOT NULL,
  `titre` varchar(150) NOT NULL,
  `description` text NOT NULL,
  `image` varchar(255) DEFAULT NULL,
  `galerie` text DEFAULT NULL,
  `theme` enum('Noël','Pâques','Classique','Évènement') DEFAULT 'Classique',
  `regime` enum('Classique','Végétarien','Vegan') DEFAULT 'Classique',
  `nb_pers_min` int(11) DEFAULT 1,
  `prix_min_pers` decimal(10,2) NOT NULL,
  `entrees` text DEFAULT NULL,
  `plats` text DEFAULT NULL,
  `desserts` text DEFAULT NULL,
  `allergenes` varchar(255) DEFAULT NULL,
  `conditions_prestation` text DEFAULT NULL,
  `stock_dispo` int(11) DEFAULT 0,
  `entree` text DEFAULT NULL,
  `plat` text DEFAULT NULL,
  `dessert` text DEFAULT NULL,
  `conditions` text DEFAULT NULL,
  `stock` int(11) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `menus`
--

INSERT INTO `menus` (`id`, `titre`, `description`, `image`, `galerie`, `theme`, `regime`, `nb_pers_min`, `prix_min_pers`, `entrees`, `plats`, `desserts`, `allergenes`, `conditions_prestation`, `stock_dispo`, `entree`, `plat`, `dessert`, `conditions`, `stock`) VALUES
(1, 'Festin de Noël Bordelais', 'Un menu d exception avec des produits locaux pour vos fêtes de fin d année.', 'menu-noel.jpg', 'huitre.png,foie gras.png,Chapon farci aux marrons.png,gratin dauphinois.png,Buche.png', 'Noël', 'Classique', 4, '45.00', 'Foie gras de canard, Huîtres du Bassin', 'Chapon farci aux marrons, Gratin dauphinois', 'Bûche chocolat noir et griottes', 'Gluten, Lactose, Coques', NULL, 34, NULL, NULL, NULL, NULL, 38);

-- --------------------------------------------------------

--
-- Structure de la table `reservations`
--

CREATE TABLE `reservations` (
  `id` int(11) NOT NULL,
  `nom_client` varchar(100) NOT NULL,
  `email_client` varchar(100) NOT NULL,
  `telephone` varchar(20) DEFAULT NULL,
  `date_evenement` date NOT NULL,
  `heure_livraison` time NOT NULL,
  `nb_personnes` int(11) NOT NULL,
  `frais_livraison` decimal(10,2) NOT NULL DEFAULT 0.00,
  `prix_total` decimal(10,2) NOT NULL DEFAULT 0.00,
  `id_menu` int(11) NOT NULL,
  `adresse_prestation` text NOT NULL,
  `ville` varchar(100) NOT NULL,
  `message` text DEFAULT NULL,
  `statut` varchar(50) DEFAULT 'En attente',
  `moyen_paiement` varchar(50) DEFAULT NULL,
  `date_creation` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

--
-- Déchargement des données de la table `reservations`
--

INSERT INTO `reservations` (`id`, `nom_client`, `email_client`, `telephone`, `date_evenement`, `heure_livraison`, `nb_personnes`, `frais_livraison`, `prix_total`, `id_menu`, `adresse_prestation`, `ville`, `message`, `statut`, `moyen_paiement`, `date_creation`) VALUES
(16, 'Zineb.C', 'zineb.chemali@outlook.fr', '0660751341', '2026-05-25', '00:00:04', 10, '0.00', '424.69', 1, '12 rue', 'Hors-Bordeaux', NULL, 'Terminée', 'Carte Bancaire', '2026-05-20 22:11:39'),
(17, 'Zineb.C', 'zineb.chemali@outlook.fr', '0660751341', '2026-05-22', '00:00:03', 5, '0.00', '225.00', 1, '12 rue', 'Bordeaux', NULL, 'Terminée', 'Carte Bancaire', '2026-05-20 22:18:45'),
(18, 'Zineb.C', 'zineb.chemali@outlook.fr', '0660751341', '2026-05-31', '00:00:03', 6, '0.00', '250.95', 1, '12 rue', 'Hors-Bordeaux', NULL, 'Terminée', 'PayPal', '2026-05-20 22:19:09');

-- --------------------------------------------------------

--
-- Structure de la table `utilisateurs`
--

CREATE TABLE `utilisateurs` (
  `id` int(11) NOT NULL,
  `nom` varchar(50) NOT NULL,
  `prenom` varchar(50) NOT NULL,
  `email` varchar(100) NOT NULL,
  `telephone` varchar(20) DEFAULT NULL,
  `adresse` text DEFAULT NULL,
  `mot_de_passe` varchar(255) NOT NULL,
  `date_inscription` timestamp NOT NULL DEFAULT current_timestamp(),
  `role` varchar(20) NOT NULL DEFAULT 'client',
  `token_recup` varchar(255) DEFAULT NULL,
  `token_expire` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `utilisateurs`
--

INSERT INTO `utilisateurs` (`id`, `nom`, `prenom`, `email`, `telephone`, `adresse`, `mot_de_passe`, `date_inscription`, `role`, `token_recup`, `token_expire`) VALUES
(2, 'Zineb.C', 'Chemali', 'zineb.chemali@outlook.fr', '0660751341', '69 rue Henri Barbusse 92230 Gennevilliers', '$2y$10$/kN6HzzmCYh9nf.G//406OpR0ZU0ZZsmp1bb9Hr8qOuVjhHnZ0zbe', '2026-05-14 11:26:01', 'client', NULL, NULL),
(3, 'Julie', 'José', 'julieetjose@viteetgourmand.fr', '0600000000', '15 rue de la paix', '$2y$10$q5AODCRj5QWQjQqEMLlQte.SH7iiSwkuoJ0Xz7RMJ/LjhA/wcTWua', '2026-05-14 18:49:03', 'admin', NULL, NULL),
(4, 'employe', 'employe', 'employe@viteetgourmand.fr', '0600000000', '15 rue de la paix', '$2y$10$Ma/99rlMX3p3LY12F6/XWuokJZa7CtGIUoi4CAIY22e2o7ybH9wgi', '2026-05-15 19:26:37', 'employe', NULL, NULL),
(16, 'user', 'dz', 'userdz92@outlook.fr', '0600000000', '15 rue de yuyu', '$2y$10$26j.D0zN9EbwQAYobMPZbeSi6AQyDZeZFs6vaNDb.UHr3PuK5zuRa', '2026-05-20 19:40:24', 'client', NULL, NULL),
(17, 'zeghlache', 'kheyreddine', 'kheyreddine93@gmail.com', '0781142194', '16 avenue louis bordes', '$2y$10$82OSb3S9.UkHoDSUlka9A.85tsxqr4F4XwrjXoVpwmMbb/XWXrlGu', '2026-05-20 19:40:54', 'client', NULL, NULL);

--
-- Index pour les tables déchargées
--

--
-- Index pour la table `avis`
--
ALTER TABLE `avis`
  ADD PRIMARY KEY (`id`);

--
-- Index pour la table `historique_statuts`
--
ALTER TABLE `historique_statuts`
  ADD PRIMARY KEY (`id`),
  ADD KEY `id_reservation` (`id_reservation`);

--
-- Index pour la table `menus`
--
ALTER TABLE `menus`
  ADD PRIMARY KEY (`id`);

--
-- Index pour la table `reservations`
--
ALTER TABLE `reservations`
  ADD PRIMARY KEY (`id`);

--
-- Index pour la table `utilisateurs`
--
ALTER TABLE `utilisateurs`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT pour les tables déchargées
--

--
-- AUTO_INCREMENT pour la table `avis`
--
ALTER TABLE `avis`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT pour la table `historique_statuts`
--
ALTER TABLE `historique_statuts`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=34;

--
-- AUTO_INCREMENT pour la table `menus`
--
ALTER TABLE `menus`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT pour la table `reservations`
--
ALTER TABLE `reservations`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

--
-- AUTO_INCREMENT pour la table `utilisateurs`
--
ALTER TABLE `utilisateurs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;

--
-- Contraintes pour les tables déchargées
--

--
-- Contraintes pour la table `historique_statuts`
--
ALTER TABLE `historique_statuts`
  ADD CONSTRAINT `historique_statuts_ibfk_1` FOREIGN KEY (`id_reservation`) REFERENCES `reservations` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
