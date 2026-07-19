/*M!999999\- enable the sandbox mode */ 
-- MariaDB dump 10.19  Distrib 10.11.14-MariaDB, for debian-linux-gnu (aarch64)
--
-- Host: localhost    Database: omega_fitness
-- ------------------------------------------------------
-- Server version	10.11.14-MariaDB-0ubuntu0.24.04.1

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `adherents`
--

DROP TABLE IF EXISTS `adherents`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `adherents` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `numero_licence` varchar(20) NOT NULL,
  `nom` varchar(50) NOT NULL,
  `prenom` varchar(50) NOT NULL,
  `email` varchar(100) NOT NULL,
  `telephone` varchar(20) DEFAULT NULL,
  `date_naissance` date DEFAULT NULL,
  `adresse` text DEFAULT NULL,
  `discipline_principale` varchar(50) DEFAULT NULL,
  `date_inscription` date DEFAULT curdate(),
  `statut` enum('actif','suspendu','archive') DEFAULT 'actif',
  `photo` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `numero_licence` (`numero_licence`),
  UNIQUE KEY `email` (`email`)
) ENGINE=InnoDB AUTO_INCREMENT=13 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `charges`
--

DROP TABLE IF EXISTS `charges`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `charges` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `date_charge` date NOT NULL,
  `type_charge` varchar(50) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `montant` decimal(10,2) NOT NULL,
  `fournisseur` varchar(100) DEFAULT NULL,
  `facture_ref` varchar(50) DEFAULT NULL,
  `categorie` enum('materiel','entretien','eau_electricite','loyer','salaires','marketing','autres') DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `cours`
--

DROP TABLE IF EXISTS `cours`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `cours` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `discipline_id` int(11) DEFAULT NULL,
  `formateur_id` int(11) DEFAULT NULL,
  `titre` varchar(100) NOT NULL,
  `description` text DEFAULT NULL,
  `niveau` varchar(30) DEFAULT NULL,
  `jour` varchar(15) DEFAULT NULL,
  `heure_debut` time DEFAULT NULL,
  `heure_fin` time DEFAULT NULL,
  `salle` varchar(50) DEFAULT NULL,
  `capacite` int(11) DEFAULT NULL,
  `inscrits` int(11) DEFAULT 0,
  `actif` tinyint(1) DEFAULT 1,
  PRIMARY KEY (`id`),
  KEY `discipline_id` (`discipline_id`),
  KEY `formateur_id` (`formateur_id`),
  CONSTRAINT `cours_ibfk_1` FOREIGN KEY (`discipline_id`) REFERENCES `disciplines` (`id`),
  CONSTRAINT `cours_ibfk_2` FOREIGN KEY (`formateur_id`) REFERENCES `formateurs` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=16 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `disciplines`
--

DROP TABLE IF EXISTS `disciplines`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `disciplines` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nom` varchar(50) NOT NULL,
  `description` text DEFAULT NULL,
  `tarif_mensuel` decimal(10,2) NOT NULL,
  `tarif_trimestriel` decimal(10,2) DEFAULT NULL,
  `tarif_annuel` decimal(10,2) DEFAULT NULL,
  `tarif_cours_libre` decimal(10,2) DEFAULT NULL,
  `age_minimum` int(11) DEFAULT NULL,
  `age_maximum` int(11) DEFAULT NULL,
  `capacite_max` int(11) DEFAULT NULL,
  `actif` tinyint(1) DEFAULT 1,
  PRIMARY KEY (`id`),
  UNIQUE KEY `nom` (`nom`)
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `facture_lignes`
--

DROP TABLE IF EXISTS `facture_lignes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `facture_lignes` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_facture` int(11) DEFAULT NULL,
  `id_produit` int(11) DEFAULT NULL,
  `designation` varchar(255) DEFAULT NULL,
  `quantite` int(11) DEFAULT NULL,
  `prix_unitaire` decimal(10,2) DEFAULT NULL,
  `sous_total` decimal(10,2) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `factures`
--

DROP TABLE IF EXISTS `factures`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `factures` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `numero` varchar(20) DEFAULT NULL,
  `id_client` int(11) DEFAULT NULL,
  `date_facture` date DEFAULT NULL,
  `statut` varchar(20) DEFAULT NULL,
  `total` decimal(10,2) DEFAULT NULL,
  `tva` decimal(5,2) DEFAULT NULL,
  `montant_tva` decimal(10,2) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `formateurs`
--

DROP TABLE IF EXISTS `formateurs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `formateurs` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `matricule` varchar(20) NOT NULL,
  `nom` varchar(50) NOT NULL,
  `prenom` varchar(50) NOT NULL,
  `email` varchar(100) NOT NULL,
  `telephone` varchar(20) DEFAULT NULL,
  `specialite` varchar(100) DEFAULT NULL,
  `diplomes` text DEFAULT NULL,
  `date_embauche` date DEFAULT NULL,
  `salaire_base` decimal(10,2) DEFAULT NULL,
  `statut` enum('actif','inactif','conge') DEFAULT 'actif',
  `photo` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `matricule` (`matricule`),
  UNIQUE KEY `email` (`email`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `inscriptions`
--

DROP TABLE IF EXISTS `inscriptions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `inscriptions` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `adherent_id` int(11) DEFAULT NULL,
  `discipline_id` int(11) DEFAULT NULL,
  `date_inscription` date DEFAULT curdate(),
  `date_debut` date DEFAULT NULL,
  `date_fin` date DEFAULT NULL,
  `type_abonnement` enum('mensuel','trimestriel','annuel','cours_libre') NOT NULL,
  `montant_total` decimal(10,2) DEFAULT NULL,
  `statut` enum('actif','expire','resilie') DEFAULT 'actif',
  PRIMARY KEY (`id`),
  KEY `adherent_id` (`adherent_id`),
  KEY `discipline_id` (`discipline_id`),
  CONSTRAINT `inscriptions_ibfk_1` FOREIGN KEY (`adherent_id`) REFERENCES `adherents` (`id`),
  CONSTRAINT `inscriptions_ibfk_2` FOREIGN KEY (`discipline_id`) REFERENCES `disciplines` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `notifications`
--

DROP TABLE IF EXISTS `notifications`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `notifications` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `titre` varchar(100) NOT NULL,
  `message` text DEFAULT NULL,
  `type` varchar(30) DEFAULT NULL,
  `destinataire` varchar(50) DEFAULT NULL,
  `date_envoi` timestamp NULL DEFAULT current_timestamp(),
  `lu` tinyint(1) DEFAULT 0,
  PRIMARY KEY (`id`),
  KEY `idx_date_envoi` (`date_envoi`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `paiements`
--

DROP TABLE IF EXISTS `paiements`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `paiements` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `adherent_id` int(11) DEFAULT NULL,
  `inscription_id` int(11) DEFAULT NULL,
  `montant` decimal(10,2) NOT NULL,
  `date_paiement` date DEFAULT curdate(),
  `mode_paiement` enum('especes','carte','cheque','virement','mobile_money') NOT NULL,
  `reference` varchar(50) DEFAULT NULL,
  `statut` enum('valide','annule','en_attente') DEFAULT 'valide',
  `observations` text DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `adherent_id` (`adherent_id`),
  KEY `inscription_id` (`inscription_id`),
  CONSTRAINT `paiements_ibfk_1` FOREIGN KEY (`adherent_id`) REFERENCES `adherents` (`id`),
  CONSTRAINT `paiements_ibfk_2` FOREIGN KEY (`inscription_id`) REFERENCES `inscriptions` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=14 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `presences`
--

DROP TABLE IF EXISTS `presences`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `presences` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `adherent_id` int(11) DEFAULT NULL,
  `cours_id` int(11) DEFAULT NULL,
  `date_seance` date DEFAULT NULL,
  `heure_arrivee` time DEFAULT NULL,
  `statut` enum('present','retard','absence') DEFAULT 'present',
  PRIMARY KEY (`id`),
  KEY `adherent_id` (`adherent_id`),
  KEY `cours_id` (`cours_id`),
  CONSTRAINT `presences_ibfk_1` FOREIGN KEY (`adherent_id`) REFERENCES `adherents` (`id`),
  CONSTRAINT `presences_ibfk_2` FOREIGN KEY (`cours_id`) REFERENCES `cours` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `produits`
--

DROP TABLE IF EXISTS `produits`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `produits` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `code_produit` varchar(20) DEFAULT NULL,
  `nom` varchar(100) NOT NULL,
  `prix` decimal(10,2) NOT NULL,
  `stock` int(11) DEFAULT 0,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8mb3 */ ;
/*!50003 SET character_set_results = utf8mb3 */ ;
/*!50003 SET collation_connection  = utf8mb3_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'STRICT_TRANS_TABLES,ERROR_FOR_DIVISION_BY_ZERO,NO_AUTO_CREATE_USER,NO_ENGINE_SUBSTITUTION' */ ;
DELIMITER ;;
/*!50003 CREATE*/ /*!50017 DEFINER=`root`@`localhost`*/ /*!50003 TRIGGER before_insert_produit
BEFORE INSERT ON produits
FOR EACH ROW
BEGIN
    SET NEW.code_produit = CONCAT(UPPER(LEFT(NEW.nom, 3)), FLOOR(RAND() * (9999 - 1000 + 1) + 1000));
END */;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2026-07-19 22:22:33
