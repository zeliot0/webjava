-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Hôte : 127.0.0.1
-- Généré le : sam. 31 jan. 2026 à 14:20
-- Version du serveur : 10.4.32-MariaDB
-- Version de PHP : 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de données : `nexa`
--

-- --------------------------------------------------------

--
-- Structure de la table `category`
--

CREATE TABLE `category` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `description` longtext DEFAULT NULL,
  `color` varchar(30) DEFAULT NULL,
  `icon` varchar(50) DEFAULT NULL,
  `is_active` tinyint(4) NOT NULL,
  `position` int(11) DEFAULT NULL,
  `visibility` varchar(20) DEFAULT NULL,
  `task_limit` int(11) DEFAULT NULL,
  `create_at` datetime NOT NULL,
  `update_at` datetime DEFAULT NULL,
  `no` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `category`
--

INSERT INTO `category` (`id`, `name`, `description`, `color`, `icon`, `is_active`, `position`, `visibility`, `task_limit`, `create_at`, `update_at`, `no`) VALUES
(1, 'animal', 'dd', '#b01c1c', 'dd', 1, 1, 'public', 2, '2026-01-30 15:20:00', '2026-02-08 15:21:00', ''),
(2, 'sport', NULL, '#05fadd', NULL, 1, 1, 'public', 2, '2026-01-30 17:35:56', '2026-01-30 17:35:56', 'CAT-2384CA8F'),
(3, 'safa', NULL, '#392de6', NULL, 1, 2, 'public', 1, '2026-01-30 17:56:34', '2026-01-30 17:56:34', 'CAT-32FC1123'),
(4, 'gaming', NULL, '#ff4791', 'fa-solid', 0, 1, 'private', 1, '2026-01-30 20:32:23', '2026-01-30 20:32:23', 'CAT-A469B84A'),
(5, 'mariem', NULL, '#2eff58', NULL, 1, NULL, NULL, NULL, '2026-01-30 21:30:51', '2026-01-30 21:30:51', 'CAT-52315B94'),
(6, 'maram', NULL, '#d0ca0b', NULL, 1, NULL, 'public', NULL, '2026-01-30 22:38:50', '2026-01-30 22:38:50', 'CAT-805E606C');

-- --------------------------------------------------------

--
-- Structure de la table `doctrine_migration_versions`
--

CREATE TABLE `doctrine_migration_versions` (
  `version` varchar(191) NOT NULL,
  `executed_at` datetime DEFAULT NULL,
  `execution_time` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `doctrine_migration_versions`
--

INSERT INTO `doctrine_migration_versions` (`version`, `executed_at`, `execution_time`) VALUES
('DoctrineMigrations\\Version20260129182445', '2026-01-29 19:24:58', 152),
('DoctrineMigrations\\Version20260129192919', '2026-01-29 20:29:31', 78);

-- --------------------------------------------------------

--
-- Structure de la table `messenger_messages`
--

CREATE TABLE `messenger_messages` (
  `id` bigint(20) NOT NULL,
  `body` longtext NOT NULL,
  `headers` longtext NOT NULL,
  `queue_name` varchar(190) NOT NULL,
  `created_at` datetime NOT NULL,
  `available_at` datetime NOT NULL,
  `delivered_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `task`
--

CREATE TABLE `task` (
  `id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `description` longtext DEFAULT NULL,
  `status` varchar(20) NOT NULL,
  `priority` varchar(20) NOT NULL,
  `due_at` date DEFAULT NULL,
  `create_at` datetime NOT NULL,
  `update_at` datetime NOT NULL,
  `category_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `task`
--

INSERT INTO `task` (`id`, `title`, `description`, `status`, `priority`, `due_at`, `create_at`, `update_at`, `category_id`) VALUES
(16, 'study', 'math+java', 'doing', 'high', '2026-01-30', '2026-01-30 16:33:49', '2026-01-30 22:40:45', NULL),
(17, 'sport', 'foot', 'done', 'med', '2026-01-30', '2026-01-30 17:36:17', '2026-01-30 22:42:28', 2),
(18, 'dzd', 'dzdzd', 'todo', 'med', NULL, '2026-01-30 17:37:00', '2026-01-30 22:45:32', 1),
(19, 'zddz', 'vrvfv', 'todo', 'med', NULL, '2026-01-30 17:37:11', '2026-01-30 22:42:24', 2),
(21, 'fifa', NULL, 'doing', 'low', '2026-01-30', '2026-01-30 20:32:36', '2026-01-30 22:40:48', 4),
(22, 'ddddddd', NULL, 'todo', 'med', '2026-01-30', '2026-01-30 21:31:06', '2026-01-30 22:42:24', 5),
(23, 'Rayen', 'L3ned w ksou7yet ras', 'doing', 'high', '2025-06-28', '2026-01-30 22:40:25', '2026-01-30 22:42:27', 6);

--
-- Index pour les tables déchargées
--

--
-- Index pour la table `category`
--
ALTER TABLE `category`
  ADD PRIMARY KEY (`id`);

--
-- Index pour la table `doctrine_migration_versions`
--
ALTER TABLE `doctrine_migration_versions`
  ADD PRIMARY KEY (`version`);

--
-- Index pour la table `messenger_messages`
--
ALTER TABLE `messenger_messages`
  ADD PRIMARY KEY (`id`),
  ADD KEY `IDX_75EA56E0FB7336F0E3BD61CE16BA31DBBF396750` (`queue_name`,`available_at`,`delivered_at`,`id`);

--
-- Index pour la table `task`
--
ALTER TABLE `task`
  ADD PRIMARY KEY (`id`),
  ADD KEY `IDX_527EDB2512469DE2` (`category_id`);

--
-- AUTO_INCREMENT pour les tables déchargées
--

--
-- AUTO_INCREMENT pour la table `category`
--
ALTER TABLE `category`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT pour la table `messenger_messages`
--
ALTER TABLE `messenger_messages`
  MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `task`
--
ALTER TABLE `task`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=24;

--
-- Contraintes pour les tables déchargées
--

--
-- Contraintes pour la table `task`
--
ALTER TABLE `task`
  ADD CONSTRAINT `FK_527EDB2512469DE2` FOREIGN KEY (`category_id`) REFERENCES `category` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
