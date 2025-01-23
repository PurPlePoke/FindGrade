-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Hôte : 127.0.0.1
-- Généré le : sam. 15 juin 2024 à 19:04
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
-- Base de données : `findgrade`
--

-- --------------------------------------------------------

--
-- Structure de la table `administrateur`
--

CREATE TABLE `administrateur` (
  `ID_Administrateur` int(11) NOT NULL,
  `ID_Utilisateur` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Structure de la table `enseignant`
--

CREATE TABLE `enseignant` (
  `ID_Enseignant` int(11) NOT NULL,
  `ID_Utilisateur` int(11) NOT NULL,
  `Statut` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `enseignant`
--

INSERT INTO `enseignant` (`ID_Enseignant`, `ID_Utilisateur`, `Statut`) VALUES
(1, 7, 'Professeur'),
(2, 8, 'Professeur'),
(3, 16, 'professeur'),
(4, 25, 'intervenant');

-- --------------------------------------------------------

--
-- Structure de la table `formation`
--

CREATE TABLE `formation` (
  `ID_Formation` int(11) NOT NULL,
  `Nom_Formation` varchar(255) NOT NULL,
  `Sigle` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `formation`
--

INSERT INTO `formation` (`ID_Formation`, `Nom_Formation`, `Sigle`) VALUES
(2, 'Métier du Multimédia et de l\'Internet', 'MMI'),
(3, 'Gestion des Entreprises et des Administrations', 'GEA');

-- --------------------------------------------------------

--
-- Structure de la table `groupetd`
--

CREATE TABLE `groupetd` (
  `ID_GroupeTD` int(11) NOT NULL,
  `Nom_GroupeTD` varchar(255) NOT NULL,
  `ID_Promotion` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Structure de la table `groupetp`
--

CREATE TABLE `groupetp` (
  `ID_GroupeTP` int(11) NOT NULL,
  `Nom_GroupeTP` varchar(255) NOT NULL,
  `ID_GroupeTD` int(11) NOT NULL,
  `MaxÉtudiants` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Structure de la table `inscription`
--

CREATE TABLE `inscription` (
  `ID_Inscription` int(11) NOT NULL,
  `ID_Étudiant` int(11) DEFAULT NULL,
  `ID_Ressource` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `inscription`
--

INSERT INTO `inscription` (`ID_Inscription`, `ID_Étudiant`, `ID_Ressource`) VALUES
(16, 12, 1),
(17, 12, 4),
(18, 12, 2),
(19, 12, 3);

-- --------------------------------------------------------

--
-- Structure de la table `note`
--

CREATE TABLE `note` (
  `ID_Note` int(11) NOT NULL,
  `ID_Étudiant` int(11) NOT NULL,
  `ID_Ressource` int(11) NOT NULL,
  `ID_Enseignant` int(11) NOT NULL,
  `ID_Évaluation` int(11) NOT NULL,
  `Note` float NOT NULL,
  `Coefficient` float NOT NULL,
  `Date` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `note`
--

INSERT INTO `note` (`ID_Note`, `ID_Étudiant`, `ID_Ressource`, `ID_Enseignant`, `ID_Évaluation`, `Note`, `Coefficient`, `Date`) VALUES
(16, 5, 3, 3, 2, 16, 4, '2024-06-15'),
(23, 5, 3, 3, 2, 15, 5, '2024-06-15');

-- --------------------------------------------------------

--
-- Structure de la table `promotion`
--

CREATE TABLE `promotion` (
  `ID_Promotion` int(11) NOT NULL,
  `Année` int(11) NOT NULL,
  `ID_Formation` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `promotion`
--

INSERT INTO `promotion` (`ID_Promotion`, `Année`, `ID_Formation`) VALUES
(1, 2024, 2),
(2, 2024, 3);

-- --------------------------------------------------------

--
-- Structure de la table `ressource`
--

CREATE TABLE `ressource` (
  `ID_Ressource` int(11) NOT NULL,
  `Nom_Ressource` varchar(255) NOT NULL,
  `Semestre` int(11) NOT NULL,
  `ID_UE` int(11) NOT NULL,
  `ID_Enseignant` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `ressource`
--

INSERT INTO `ressource` (`ID_Ressource`, `Nom_Ressource`, `Semestre`, `ID_UE`, `ID_Enseignant`) VALUES
(1, 'Anglais', 1, 1, 1),
(2, 'Intégration', 1, 2, 2),
(3, 'Hébergement', 1, 2, 3),
(4, 'Ecriture multimédia et narration', 2, 1, 4);

-- --------------------------------------------------------

--
-- Structure de la table `sitecolors`
--

CREATE TABLE `sitecolors` (
  `ID` int(11) NOT NULL,
  `GaucheColor` varchar(7) NOT NULL,
  `DroiteColor` varchar(7) NOT NULL,
  `TextColor` varchar(7) NOT NULL,
  `ButtonColor` varchar(7) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Structure de la table `ue`
--

CREATE TABLE `ue` (
  `ID_UE` int(11) NOT NULL,
  `Nom_UE` varchar(255) NOT NULL,
  `ID_Formation` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `ue`
--

INSERT INTO `ue` (`ID_UE`, `Nom_UE`, `ID_Formation`) VALUES
(1, 'Exprimer', 2),
(2, 'Développer', 2),
(3, 'Concevoir', 2),
(4, 'Comprendre', 2),
(5, 'Entreprendre', 2);

-- --------------------------------------------------------

--
-- Structure de la table `utilisateur`
--

CREATE TABLE `utilisateur` (
  `ID_Utilisateur` int(11) NOT NULL,
  `Nom` varchar(255) NOT NULL,
  `Prénom` varchar(255) NOT NULL,
  `Email` varchar(255) NOT NULL,
  `Mot_de_passe` varchar(255) NOT NULL,
  `Type` varchar(50) NOT NULL,
  `Login` varchar(255) DEFAULT NULL,
  `Genre` varchar(10) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `utilisateur`
--

INSERT INTO `utilisateur` (`ID_Utilisateur`, `Nom`, `Prénom`, `Email`, `Mot_de_passe`, `Type`, `Login`, `Genre`) VALUES
(7, 'Al Salti', 'Nadia', 'nadia.al-salti@univ-eiffel.fr', '$2y$10$ZynRxt3xj.4W8rb20wz7RuN7uNkO.itoTGaV/hZ4rceTJmgIXUExG', 'professeur', 'nadia.al salti', 'Madame'),
(8, 'Laroussi', 'Reda', 'abdelatif-reda.laroussi@univ-eiffel.fr', '$2y$10$3KCi1sGxjU/zFBfS5s1xReK9qo4PNkidE528MsrvbxMfq67TEpstW', 'professeur', 'reda.laroussi', 'Monsieur'),
(10, 'User', 'Admin', 'admin@example.com', '$2y$10$lejfvKc94Q1DAAeteLjf8emsq4RMeW7gtQb3xXSObOe8o8mFsX50a', 'admin', 'admin.user', NULL),
(15, 'Martin', 'Claire', 'claire.martin@gmail.com', '$2y$10$dBdLFmWBlvty6D02RTR9P.sCD2YEoa0EtyJkxGxSnWDtdOb57q4r6', 'eleve', 'claire.martin', 'Madame'),
(16, 'Zaidi', 'Fares', 'fares.zaidi@univ-eiffel.fr', '$2y$10$hx69cGnCtEXpoGt.Ji8xSOKXX9wtkI/pYWhGtlbWyLLgqeRuv.Vcm', 'professeur', 'fares.zaidi', 'Monsieur'),
(25, 'Audran', 'Peronne', 'audran.peronne@gmail.com', '$2y$10$GMZvk5uWIlzhi/1dTXl6O.DW07UDSv/lUCev00UE0wPD5dJJPkvie', 'professeur', 'peronne.audran', 'Monsieur'),
(27, 'user', 'eleve', 'eleve.user@gmail.com', '$2y$10$gI2./CgWYXLHYc2oOcRtguHVmvAqmpb6qOkEpZTOb9s7MWX0MJhPy', 'eleve', 'eleve.user', 'Monsieur');

-- --------------------------------------------------------

--
-- Structure de la table `étudiant`
--

CREATE TABLE `étudiant` (
  `ID_Étudiant` int(11) NOT NULL,
  `ID_Utilisateur` int(11) NOT NULL,
  `ID_Promotion` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `étudiant`
--

INSERT INTO `étudiant` (`ID_Étudiant`, `ID_Utilisateur`, `ID_Promotion`) VALUES
(5, 15, 1),
(12, 27, 1);

-- --------------------------------------------------------

--
-- Structure de la table `évaluation`
--

CREATE TABLE `évaluation` (
  `ID_Évaluation` int(11) NOT NULL,
  `ID_Promotion` int(11) NOT NULL,
  `ID_Enseignant` int(11) NOT NULL,
  `ID_Ressource` int(11) NOT NULL,
  `Horaire` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `évaluation`
--

INSERT INTO `évaluation` (`ID_Évaluation`, `ID_Promotion`, `ID_Enseignant`, `ID_Ressource`, `Horaire`) VALUES
(1, 1, 1, 1, '2024-06-10 10:00:00'),
(2, 1, 3, 3, '2024-06-15 11:15:42');

--
-- Index pour les tables déchargées
--

--
-- Index pour la table `administrateur`
--
ALTER TABLE `administrateur`
  ADD PRIMARY KEY (`ID_Administrateur`),
  ADD KEY `ID_Utilisateur` (`ID_Utilisateur`);

--
-- Index pour la table `enseignant`
--
ALTER TABLE `enseignant`
  ADD PRIMARY KEY (`ID_Enseignant`),
  ADD KEY `ID_Utilisateur` (`ID_Utilisateur`);

--
-- Index pour la table `formation`
--
ALTER TABLE `formation`
  ADD PRIMARY KEY (`ID_Formation`);

--
-- Index pour la table `groupetd`
--
ALTER TABLE `groupetd`
  ADD PRIMARY KEY (`ID_GroupeTD`),
  ADD KEY `ID_Promotion` (`ID_Promotion`);

--
-- Index pour la table `groupetp`
--
ALTER TABLE `groupetp`
  ADD PRIMARY KEY (`ID_GroupeTP`),
  ADD KEY `ID_GroupeTD` (`ID_GroupeTD`);

--
-- Index pour la table `inscription`
--
ALTER TABLE `inscription`
  ADD PRIMARY KEY (`ID_Inscription`),
  ADD KEY `ID_Étudiant` (`ID_Étudiant`),
  ADD KEY `ID_Ressource` (`ID_Ressource`);

--
-- Index pour la table `note`
--
ALTER TABLE `note`
  ADD PRIMARY KEY (`ID_Note`),
  ADD KEY `ID_Étudiant` (`ID_Étudiant`),
  ADD KEY `ID_Ressource` (`ID_Ressource`),
  ADD KEY `ID_Enseignant` (`ID_Enseignant`),
  ADD KEY `ID_Évaluation` (`ID_Évaluation`);

--
-- Index pour la table `promotion`
--
ALTER TABLE `promotion`
  ADD PRIMARY KEY (`ID_Promotion`),
  ADD KEY `ID_Formation` (`ID_Formation`);

--
-- Index pour la table `ressource`
--
ALTER TABLE `ressource`
  ADD PRIMARY KEY (`ID_Ressource`),
  ADD KEY `ID_UE` (`ID_UE`),
  ADD KEY `ID_Enseignant` (`ID_Enseignant`);

--
-- Index pour la table `sitecolors`
--
ALTER TABLE `sitecolors`
  ADD PRIMARY KEY (`ID`);

--
-- Index pour la table `ue`
--
ALTER TABLE `ue`
  ADD PRIMARY KEY (`ID_UE`),
  ADD KEY `ID_Formation` (`ID_Formation`);

--
-- Index pour la table `utilisateur`
--
ALTER TABLE `utilisateur`
  ADD PRIMARY KEY (`ID_Utilisateur`),
  ADD UNIQUE KEY `Email` (`Email`),
  ADD UNIQUE KEY `Login` (`Login`);

--
-- Index pour la table `étudiant`
--
ALTER TABLE `étudiant`
  ADD PRIMARY KEY (`ID_Étudiant`),
  ADD KEY `ID_Utilisateur` (`ID_Utilisateur`),
  ADD KEY `ID_Promotion` (`ID_Promotion`);

--
-- Index pour la table `évaluation`
--
ALTER TABLE `évaluation`
  ADD PRIMARY KEY (`ID_Évaluation`),
  ADD KEY `ID_Promotion` (`ID_Promotion`),
  ADD KEY `ID_Enseignant` (`ID_Enseignant`),
  ADD KEY `ID_Ressource` (`ID_Ressource`);

--
-- AUTO_INCREMENT pour les tables déchargées
--

--
-- AUTO_INCREMENT pour la table `administrateur`
--
ALTER TABLE `administrateur`
  MODIFY `ID_Administrateur` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `enseignant`
--
ALTER TABLE `enseignant`
  MODIFY `ID_Enseignant` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT pour la table `formation`
--
ALTER TABLE `formation`
  MODIFY `ID_Formation` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT pour la table `groupetd`
--
ALTER TABLE `groupetd`
  MODIFY `ID_GroupeTD` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `groupetp`
--
ALTER TABLE `groupetp`
  MODIFY `ID_GroupeTP` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `inscription`
--
ALTER TABLE `inscription`
  MODIFY `ID_Inscription` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=20;

--
-- AUTO_INCREMENT pour la table `note`
--
ALTER TABLE `note`
  MODIFY `ID_Note` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=26;

--
-- AUTO_INCREMENT pour la table `promotion`
--
ALTER TABLE `promotion`
  MODIFY `ID_Promotion` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT pour la table `ressource`
--
ALTER TABLE `ressource`
  MODIFY `ID_Ressource` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT pour la table `sitecolors`
--
ALTER TABLE `sitecolors`
  MODIFY `ID` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `ue`
--
ALTER TABLE `ue`
  MODIFY `ID_UE` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT pour la table `utilisateur`
--
ALTER TABLE `utilisateur`
  MODIFY `ID_Utilisateur` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=28;

--
-- AUTO_INCREMENT pour la table `étudiant`
--
ALTER TABLE `étudiant`
  MODIFY `ID_Étudiant` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT pour la table `évaluation`
--
ALTER TABLE `évaluation`
  MODIFY `ID_Évaluation` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- Contraintes pour les tables déchargées
--

--
-- Contraintes pour la table `administrateur`
--
ALTER TABLE `administrateur`
  ADD CONSTRAINT `administrateur_ibfk_1` FOREIGN KEY (`ID_Utilisateur`) REFERENCES `utilisateur` (`ID_Utilisateur`);

--
-- Contraintes pour la table `enseignant`
--
ALTER TABLE `enseignant`
  ADD CONSTRAINT `enseignant_ibfk_1` FOREIGN KEY (`ID_Utilisateur`) REFERENCES `utilisateur` (`ID_Utilisateur`);

--
-- Contraintes pour la table `groupetd`
--
ALTER TABLE `groupetd`
  ADD CONSTRAINT `groupetd_ibfk_1` FOREIGN KEY (`ID_Promotion`) REFERENCES `promotion` (`ID_Promotion`);

--
-- Contraintes pour la table `groupetp`
--
ALTER TABLE `groupetp`
  ADD CONSTRAINT `groupetp_ibfk_1` FOREIGN KEY (`ID_GroupeTD`) REFERENCES `groupetd` (`ID_GroupeTD`);

--
-- Contraintes pour la table `inscription`
--
ALTER TABLE `inscription`
  ADD CONSTRAINT `inscription_ibfk_1` FOREIGN KEY (`ID_Étudiant`) REFERENCES `étudiant` (`ID_Étudiant`),
  ADD CONSTRAINT `inscription_ibfk_2` FOREIGN KEY (`ID_Ressource`) REFERENCES `ressource` (`ID_Ressource`);

--
-- Contraintes pour la table `note`
--
ALTER TABLE `note`
  ADD CONSTRAINT `note_ibfk_1` FOREIGN KEY (`ID_Étudiant`) REFERENCES `étudiant` (`ID_Étudiant`),
  ADD CONSTRAINT `note_ibfk_2` FOREIGN KEY (`ID_Ressource`) REFERENCES `ressource` (`ID_Ressource`),
  ADD CONSTRAINT `note_ibfk_3` FOREIGN KEY (`ID_Enseignant`) REFERENCES `enseignant` (`ID_Enseignant`),
  ADD CONSTRAINT `note_ibfk_4` FOREIGN KEY (`ID_Évaluation`) REFERENCES `évaluation` (`ID_Évaluation`);

--
-- Contraintes pour la table `promotion`
--
ALTER TABLE `promotion`
  ADD CONSTRAINT `promotion_ibfk_1` FOREIGN KEY (`ID_Formation`) REFERENCES `formation` (`ID_Formation`);

--
-- Contraintes pour la table `ressource`
--
ALTER TABLE `ressource`
  ADD CONSTRAINT `ressource_ibfk_1` FOREIGN KEY (`ID_UE`) REFERENCES `ue` (`ID_UE`),
  ADD CONSTRAINT `ressource_ibfk_2` FOREIGN KEY (`ID_Enseignant`) REFERENCES `enseignant` (`ID_Enseignant`);

--
-- Contraintes pour la table `ue`
--
ALTER TABLE `ue`
  ADD CONSTRAINT `ue_ibfk_1` FOREIGN KEY (`ID_Formation`) REFERENCES `formation` (`ID_Formation`);

--
-- Contraintes pour la table `étudiant`
--
ALTER TABLE `étudiant`
  ADD CONSTRAINT `étudiant_ibfk_1` FOREIGN KEY (`ID_Utilisateur`) REFERENCES `utilisateur` (`ID_Utilisateur`),
  ADD CONSTRAINT `étudiant_ibfk_2` FOREIGN KEY (`ID_Promotion`) REFERENCES `promotion` (`ID_Promotion`);

--
-- Contraintes pour la table `évaluation`
--
ALTER TABLE `évaluation`
  ADD CONSTRAINT `évaluation_ibfk_1` FOREIGN KEY (`ID_Promotion`) REFERENCES `promotion` (`ID_Promotion`),
  ADD CONSTRAINT `évaluation_ibfk_2` FOREIGN KEY (`ID_Enseignant`) REFERENCES `enseignant` (`ID_Enseignant`),
  ADD CONSTRAINT `évaluation_ibfk_3` FOREIGN KEY (`ID_Ressource`) REFERENCES `ressource` (`ID_Ressource`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
