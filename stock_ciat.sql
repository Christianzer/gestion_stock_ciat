-- phpMyAdmin SQL Dump
-- version 5.0.4
-- https://www.phpmyadmin.net/
--
-- Hôte : localhost:3306
-- Généré le : lun. 08 nov. 2021 à 11:35
-- Version du serveur :  5.7.24
-- Version de PHP : 7.4.16

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de données : `stock_ciat`
--

-- --------------------------------------------------------

--
-- Structure de la table `clients`
--

CREATE TABLE `clients` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `matricule` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `nom` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `prenoms` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `telephone` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `statut` int(11) NOT NULL DEFAULT '1'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `clients`
--

INSERT INTO `clients` (`id`, `matricule`, `nom`, `prenoms`, `telephone`, `statut`) VALUES
(1, 'CI012547865', 'AKA', 'CHRISTIAN DE PACQUES', '0143148561', 1),
(2, 'C1AE45', 'CEDRIC', 'ANGE KOUASSI', '0143148561', 1),
(3, 'CI14786', 'DEKO', 'YVES JEAN CONSTANT', '0143148561', 1);

-- --------------------------------------------------------

--
-- Structure de la table `factures`
--

CREATE TABLE `factures` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `code_facture` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `montant_total` double(8,2) NOT NULL,
  `montant_verser` double(8,2) NOT NULL,
  `montant_rendu` double(8,2) NOT NULL,
  `matricule_clients` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `factures`
--

INSERT INTO `factures` (`id`, `code_facture`, `montant_total`, `montant_verser`, `montant_rendu`, `matricule_clients`) VALUES
(1, 'GSCIAT6GFI', 6600.00, 12000.00, 5400.00, 1),
(2, 'GSCIAT25IX', 30800.00, 31000.00, 200.00, 1);

-- --------------------------------------------------------

--
-- Structure de la table `migrations`
--

CREATE TABLE `migrations` (
  `id` int(10) UNSIGNED NOT NULL,
  `migration` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `batch` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `migrations`
--

INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES
(2, '2019_12_14_000001_create_personal_access_tokens_table', 1),
(7, '2014_10_12_000000_create_users_table', 2),
(8, '2021_11_02_215658_create_clients_table', 2),
(9, '2021_11_02_220904_create_produits_table', 2),
(10, '2021_11_02_221057_create_ventes_table', 2),
(11, '2021_11_02_222112_create_factures_table', 2);

-- --------------------------------------------------------

--
-- Structure de la table `personal_access_tokens`
--

CREATE TABLE `personal_access_tokens` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `tokenable_type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `tokenable_id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `token` varchar(64) COLLATE utf8mb4_unicode_ci NOT NULL,
  `abilities` text COLLATE utf8mb4_unicode_ci,
  `last_used_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `produits`
--

CREATE TABLE `produits` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `code_produit` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `libelle_produit` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `quantite_produit` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `prix_produit` double(8,2) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `produits`
--

INSERT INTO `produits` (`id`, `code_produit`, `libelle_produit`, `quantite_produit`, `prix_produit`, `created_at`, `updated_at`) VALUES
(1, 'ART1', 'SERVIETTE JOURS', '100', 2200.00, NULL, NULL),
(2, 'ART2', 'SERVIETTE NUIT', '100', 2200.00, NULL, NULL);

-- --------------------------------------------------------

--
-- Structure de la table `users`
--

CREATE TABLE `users` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `remember_token` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `ventes`
--

CREATE TABLE `ventes` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `code_produit` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `quantite_acheter` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `code_facture` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `total_payer` double(8,2) NOT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `ventes`
--

INSERT INTO `ventes` (`id`, `code_produit`, `quantite_acheter`, `code_facture`, `total_payer`, `created_at`, `updated_at`) VALUES
(1, 'ART2', '3', 'GSCIAT6GFI', 6600.00, '2021-11-04 19:16:44', NULL),
(2, 'ART1', '6', 'GSCIAT25IX', 13200.00, '2021-11-05 14:57:43', NULL),
(3, 'ART2', '8', 'GSCIAT25IX', 17600.00, '2021-11-05 14:57:43', NULL);

--
-- Index pour les tables déchargées
--

--
-- Index pour la table `clients`
--
ALTER TABLE `clients`
  ADD PRIMARY KEY (`id`);

--
-- Index pour la table `factures`
--
ALTER TABLE `factures`
  ADD PRIMARY KEY (`id`);

--
-- Index pour la table `migrations`
--
ALTER TABLE `migrations`
  ADD PRIMARY KEY (`id`);

--
-- Index pour la table `personal_access_tokens`
--
ALTER TABLE `personal_access_tokens`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `personal_access_tokens_token_unique` (`token`),
  ADD KEY `personal_access_tokens_tokenable_type_tokenable_id_index` (`tokenable_type`,`tokenable_id`);

--
-- Index pour la table `produits`
--
ALTER TABLE `produits`
  ADD PRIMARY KEY (`id`);

--
-- Index pour la table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `users_email_unique` (`email`);

--
-- Index pour la table `ventes`
--
ALTER TABLE `ventes`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT pour les tables déchargées
--

--
-- AUTO_INCREMENT pour la table `clients`
--
ALTER TABLE `clients`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT pour la table `factures`
--
ALTER TABLE `factures`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT pour la table `migrations`
--
ALTER TABLE `migrations`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT pour la table `personal_access_tokens`
--
ALTER TABLE `personal_access_tokens`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `produits`
--
ALTER TABLE `produits`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT pour la table `users`
--
ALTER TABLE `users`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `ventes`
--
ALTER TABLE `ventes`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
