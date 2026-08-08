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
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `adherents`
--

LOCK TABLES `adherents` WRITE;
/*!40000 ALTER TABLE `adherents` DISABLE KEYS */;
INSERT INTO `adherents` VALUES
(1,'LIC-2026-001','Diop','Mamadou','mamadou.diop@example.com','771234567',NULL,NULL,NULL,'2026-08-08','actif',NULL,'2026-08-08 00:43:13'),
(2,'LIC-2026-002','Ndiaye','Fatou','fatou.ndiaye@example.com','772345678',NULL,NULL,NULL,'2026-08-08','actif',NULL,'2026-08-08 00:43:13'),
(3,'LIC-2026-003','Sow','Abdoulaye','abdoulaye.sow@example.com','773456789',NULL,NULL,NULL,'2026-08-08','actif',NULL,'2026-08-08 00:43:13'),
(4,'LIC-2026-004','Fall','Aissatou','aissatou.fall@example.com','774567890',NULL,NULL,NULL,'2026-08-08','actif',NULL,'2026-08-08 00:43:13'),
(5,'LIC-2026-005','Ba','Ibrahima','ibrahima.ba@example.com','775678904',NULL,NULL,NULL,'2026-08-08','actif',NULL,'2026-08-08 00:43:13'),
(6,'LIC-2026-006','Mohamed','Siby','sibymohamed24@gmail.com','77 654 28 03',NULL,NULL,NULL,'2026-08-08','actif',NULL,'2026-08-08 01:06:09');
/*!40000 ALTER TABLE `adherents` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `avis_adherents`
--

DROP TABLE IF EXISTS `avis_adherents`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `avis_adherents` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `numero_licence` varchar(255) DEFAULT NULL,
  `type_avis` enum('fonctionnement','coaching','autre') DEFAULT 'fonctionnement',
  `note` tinyint(3) unsigned DEFAULT NULL CHECK (`note` between 1 and 5),
  `commentaire` text NOT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `appreciation_salle` text DEFAULT NULL,
  `ameliorations` text DEFAULT NULL,
  `accueil` text DEFAULT NULL,
  `coaching_reproches` text DEFAULT NULL,
  `coaching_compliments` text DEFAULT NULL,
  `adherent_id` int(11) DEFAULT NULL,
  `is_anonymous` tinyint(1) DEFAULT 0,
  PRIMARY KEY (`id`),
  KEY `numero_licence` (`numero_licence`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `avis_adherents`
--

LOCK TABLES `avis_adherents` WRITE;
/*!40000 ALTER TABLE `avis_adherents` DISABLE KEYS */;
INSERT INTO `avis_adherents` VALUES
(1,'LIC-2026-005','fonctionnement',5,'','2026-08-08 00:49:34','Bien équipé','Améliorer la clarté des instructions','Peut mieux faire','Manque de suivi','Diversifier les disciplines',NULL,0),
(2,'LIC-2026-006','fonctionnement',5,'','2026-08-08 01:08:35','Spacieuse','Matériel pour enfants','Peut mieux faire','Retard','Bonne harmonie avec les coachs',NULL,0);
/*!40000 ALTER TABLE `avis_adherents` ENABLE KEYS */;
UNLOCK TABLES;

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
-- Dumping data for table `charges`
--

LOCK TABLES `charges` WRITE;
/*!40000 ALTER TABLE `charges` DISABLE KEYS */;
INSERT INTO `charges` VALUES
(1,'2024-01-05','Loyer','Loyer mensuel local',500000.00,'Immobilière FALL',NULL,'loyer'),
(2,'2024-01-10','Électricité','Facture électricité janvier',150000.00,'SENELEC',NULL,'eau_electricite'),
(3,'2024-01-15','Salaires','Paiement salaires formateurs',3500000.00,'Oméga Fitness',NULL,'salaires'),
(4,'2024-02-05','Loyer','Loyer mensuel local',500000.00,'Immobilière FALL',NULL,'loyer'),
(5,'2024-02-10','Électricité','Facture électricité février',145000.00,'SENELEC',NULL,'eau_electricite'),
(6,'2024-02-20','Matériel','Achat gants de boxe',300000.00,'Decathlon',NULL,'materiel'),
(7,'2024-03-05','Loyer','Loyer mensuel local',500000.00,'Immobilière FALL',NULL,'loyer'),
(8,'2024-03-15','Marketing','Campagne publicitaire',250000.00,'SocialMedia Pro',NULL,'marketing'),
(9,'2024-04-05','Loyer','Loyer mensuel local',500000.00,'Immobilière FALL',NULL,'loyer'),
(10,'2024-04-20','Entretien','Nettoyage et entretien',100000.00,'Clean Service',NULL,'entretien');
/*!40000 ALTER TABLE `charges` ENABLE KEYS */;
UNLOCK TABLES;

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
-- Dumping data for table `cours`
--

LOCK TABLES `cours` WRITE;
/*!40000 ALTER TABLE `cours` DISABLE KEYS */;
INSERT INTO `cours` VALUES
(1,1,1,'Boxe Débutants',NULL,NULL,'LUNDI','18:00:00','19:30:00','Salle A - Ring',20,15,1),
(2,1,1,'Boxe Avancés',NULL,NULL,'MERCREDI','19:30:00','21:00:00','Salle A - Ring',15,12,1),
(3,2,2,'Karaté Enfants',NULL,NULL,'MARDI','17:00:00','18:00:00','Salle B - Dojo',15,14,1),
(4,2,2,'Karaté Adultes',NULL,NULL,'JEUDI','19:00:00','20:30:00','Salle B - Dojo',20,18,1),
(5,3,3,'JJB All Levels',NULL,NULL,'LUNDI','20:00:00','21:30:00','Salle C - Tapis',15,12,1),
(6,3,3,'JJB No Gi',NULL,NULL,'VENDREDI','19:00:00','20:30:00','Salle C - Tapis',15,10,1),
(7,4,5,'Muay Thai Débutants',NULL,NULL,'MARDI','19:00:00','20:30:00','Salle A - Ring',20,16,1),
(8,4,5,'Muay Thai Avancés',NULL,NULL,'JEUDI','20:30:00','22:00:00','Salle A - Ring',15,11,1),
(9,5,4,'CrossFit Morning',NULL,NULL,'LUNDI','06:00:00','07:00:00','CrossFit Box',12,10,1),
(10,5,4,'CrossFit Soir',NULL,NULL,'MERCREDI','18:00:00','19:00:00','CrossFit Box',15,13,1),
(11,5,4,'CrossFit Weekend',NULL,NULL,'SAMEDI','09:00:00','10:30:00','CrossFit Box',20,17,1),
(12,6,6,'Yoga Doux',NULL,NULL,'LUNDI','08:00:00','09:00:00','Studio Yoga',15,12,1),
(13,6,6,'Yoga Flow',NULL,NULL,'MERCREDI','17:00:00','18:30:00','Studio Yoga',20,18,1),
(14,7,8,'Kickboxing',NULL,NULL,'MARDI','18:30:00','20:00:00','Salle A - Ring',20,14,1),
(15,10,7,'MMA Débutants',NULL,NULL,'MERCREDI','20:00:00','21:30:00','Salle MMA',15,10,1);
/*!40000 ALTER TABLE `cours` ENABLE KEYS */;
UNLOCK TABLES;

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
-- Dumping data for table `disciplines`
--

LOCK TABLES `disciplines` WRITE;
/*!40000 ALTER TABLE `disciplines` DISABLE KEYS */;
INSERT INTO `disciplines` VALUES
(1,'Boxe Anglaise','Boxe pieds-poings traditionnelle',45000.00,120000.00,420000.00,8000.00,12,NULL,30,1),
(2,'Karaté','Art martial traditionnel japonais',40000.00,110000.00,380000.00,7000.00,6,NULL,25,1),
(3,'Jiu-Jitsu Brésilien','Art martial au sol',50000.00,135000.00,480000.00,10000.00,14,NULL,20,1),
(4,'Muay Thai','Boxe thaïlandaise',48000.00,130000.00,450000.00,9000.00,12,NULL,25,1),
(5,'CrossFit','Entraînement fonctionnel intensif',55000.00,150000.00,520000.00,12000.00,16,NULL,20,1),
(6,'Yoga','Bien-être et souplesse',35000.00,95000.00,320000.00,6000.00,12,NULL,20,1),
(7,'Kickboxing','Boxe pieds-poings',47000.00,125000.00,440000.00,8500.00,12,NULL,25,1),
(8,'Aïkido','Art martial de défense',42000.00,115000.00,400000.00,7500.00,10,NULL,20,1),
(9,'Taekwondo','Art martial coréen',43000.00,118000.00,410000.00,7800.00,8,NULL,25,1),
(10,'MMA','Mixed Martial Arts',65000.00,180000.00,620000.00,15000.00,16,NULL,15,1);
/*!40000 ALTER TABLE `disciplines` ENABLE KEYS */;
UNLOCK TABLES;

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
-- Dumping data for table `facture_lignes`
--

LOCK TABLES `facture_lignes` WRITE;
/*!40000 ALTER TABLE `facture_lignes` DISABLE KEYS */;
INSERT INTO `facture_lignes` VALUES
(1,1,1,'Huile de massage relaxante',2,5000.00,10000.00),
(2,1,4,'Boisson énergétique protéinée',3,2500.00,7500.00);
/*!40000 ALTER TABLE `facture_lignes` ENABLE KEYS */;
UNLOCK TABLES;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8mb3 */ ;
/*!50003 SET character_set_results = utf8mb3 */ ;
/*!50003 SET collation_connection  = utf8mb3_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'STRICT_TRANS_TABLES,ERROR_FOR_DIVISION_BY_ZERO,NO_AUTO_CREATE_USER,NO_ENGINE_SUBSTITUTION' */ ;
DELIMITER ;;
/*!50003 CREATE*/ /*!50017 DEFINER=`root`@`localhost`*/ /*!50003 TRIGGER after_insert_facture_ligne
AFTER INSERT ON facture_lignes
FOR EACH ROW
BEGIN
    UPDATE produits 
    SET stock = stock - NEW.quantite 
    WHERE id = NEW.id_produit;
END */;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;

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
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `factures`
--

LOCK TABLES `factures` WRITE;
/*!40000 ALTER TABLE `factures` DISABLE KEYS */;
INSERT INTO `factures` VALUES
(1,'FAC-2026-001',1,'2026-07-19','payee',17000.00,18.00,3060.00),
(2,'1',NULL,'2026-07-19','payee',588.00,NULL,NULL);
/*!40000 ALTER TABLE `factures` ENABLE KEYS */;
UNLOCK TABLES;

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
-- Dumping data for table `formateurs`
--

LOCK TABLES `formateurs` WRITE;
/*!40000 ALTER TABLE `formateurs` DISABLE KEYS */;
INSERT INTO `formateurs` VALUES
(1,'FM-2024-001','Diallo','Mamadou','m.diallo@omegafitness.com','771234567','Boxe Anglaise - Pro','Champion National, Diplôme d\'État BPJEPS','2020-01-15',450000.00,'actif',NULL,'2026-04-08 22:41:47'),
(2,'FM-2024-002','Sarr','Fatou','f.sarr@omegafitness.com','772345678','Karaté - 5e Dan','Ceinture Noire 5e Dan, Championne d\'Afrique','2019-03-10',500000.00,'actif',NULL,'2026-04-08 22:41:47'),
(3,'FM-2024-003','Ndiaye','Ousmane','o.ndiaye@omegafitness.com','773456789','Jiu-Jitsu - Ceinture Noire','Ceinture Noire 2e Degré, Champion National','2021-06-20',480000.00,'actif',NULL,'2026-04-08 22:41:47'),
(4,'FM-2024-004','Fall','Aissatou','a.fall@omegafitness.com','774567890','CrossFit - Coach L3','CrossFit Level 3 Trainer, Nutrition Coach','2022-01-05',550000.00,'actif',NULL,'2026-04-08 22:41:47'),
(5,'FM-2024-005','Gueye','Pape','p.gueye@omegafitness.com','775678901','Muay Thai','Champion National, Instructeur certifié Thaïlande','2020-09-12',470000.00,'actif',NULL,'2026-04-08 22:41:47'),
(6,'FM-2024-006','Diop','Marième','m.diop@omegafitness.com','776789012','Yoga & Méditation','Certifiée Yoga Alliance RYT 500','2023-02-18',400000.00,'actif',NULL,'2026-04-08 22:41:47'),
(7,'FM-2024-007','Sy','Abdoulaye','a.sy@omegafitness.com','777890123','MMA - Coach','Ceinture Noire JJB, Boxe Thaï, Lutte','2021-11-30',600000.00,'actif',NULL,'2026-04-08 22:41:47'),
(8,'FM-2024-008','Ngom','Aminata','a.ngom@omegafitness.com','778901234','Kickboxing','Championne Nationale, Coach K1','2022-07-22',460000.00,'conge',NULL,'2026-04-08 22:41:47');
/*!40000 ALTER TABLE `formateurs` ENABLE KEYS */;
UNLOCK TABLES;

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
-- Dumping data for table `inscriptions`
--

LOCK TABLES `inscriptions` WRITE;
/*!40000 ALTER TABLE `inscriptions` DISABLE KEYS */;
INSERT INTO `inscriptions` VALUES
(1,1,1,'2026-04-08','2024-01-10','2025-01-10','annuel',420000.00,'actif'),
(2,2,2,'2026-04-08','2024-01-15','2024-12-15','annuel',380000.00,'actif'),
(3,3,3,'2026-04-08','2024-02-01','2024-05-01','trimestriel',135000.00,'actif'),
(4,4,5,'2026-04-08','2024-02-10','2024-03-10','mensuel',55000.00,'actif'),
(5,5,4,'2026-04-08','2024-03-01','2024-06-01','trimestriel',130000.00,'actif'),
(6,6,6,'2026-04-08','2024-03-15','2024-04-15','mensuel',35000.00,'actif'),
(7,7,7,'2026-04-08','2024-04-01','2025-04-01','annuel',440000.00,'actif'),
(8,8,10,'2026-04-08','2024-04-10','2024-07-10','trimestriel',180000.00,'actif'),
(9,9,1,'2026-04-08','2024-05-01','2024-08-01','trimestriel',120000.00,'actif'),
(10,11,3,'2026-04-08','2024-06-01','2024-09-01','trimestriel',135000.00,'actif');
/*!40000 ALTER TABLE `inscriptions` ENABLE KEYS */;
UNLOCK TABLES;

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
-- Dumping data for table `notifications`
--

LOCK TABLES `notifications` WRITE;
/*!40000 ALTER TABLE `notifications` DISABLE KEYS */;
INSERT INTO `notifications` VALUES
(1,'Séance de travail ','Organisation marathon ','info','tous','2026-07-19 11:17:49',0);
/*!40000 ALTER TABLE `notifications` ENABLE KEYS */;
UNLOCK TABLES;

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
-- Dumping data for table `paiements`
--

LOCK TABLES `paiements` WRITE;
/*!40000 ALTER TABLE `paiements` DISABLE KEYS */;
INSERT INTO `paiements` VALUES
(1,1,1,420000.00,'2024-01-10','virement','TRF-001','valide',NULL),
(2,2,2,380000.00,'2024-01-15','especes','ESP-001','valide',NULL),
(3,3,3,135000.00,'2024-02-01','carte','CRD-001','valide',NULL),
(4,4,4,55000.00,'2024-02-10','mobile_money','MM-001','valide',NULL),
(5,5,5,130000.00,'2024-03-01','cheque','CHQ-001','valide',NULL),
(6,6,6,35000.00,'2024-03-15','especes','ESP-002','valide',NULL),
(7,7,7,440000.00,'2024-04-01','virement','TRF-002','valide',NULL),
(8,8,8,180000.00,'2024-04-10','carte','CRD-002','valide',NULL),
(9,9,9,120000.00,'2024-05-01','mobile_money','MM-002','valide',NULL),
(10,11,10,135000.00,'2024-06-01','especes','ESP-003','valide',NULL),
(11,1,NULL,50000.00,'2024-06-15','carte','CRD-003','valide',NULL),
(12,5,NULL,80000.00,'2024-06-20','mobile_money','MM-003','valide',NULL),
(13,7,NULL,35000.00,'2026-07-19','especes','','valide','');
/*!40000 ALTER TABLE `paiements` ENABLE KEYS */;
UNLOCK TABLES;

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
-- Dumping data for table `presences`
--

LOCK TABLES `presences` WRITE;
/*!40000 ALTER TABLE `presences` DISABLE KEYS */;
INSERT INTO `presences` VALUES
(1,1,1,'2024-06-03','17:55:00','present'),
(2,2,3,'2024-06-04','16:50:00','present'),
(3,3,5,'2024-06-03','19:55:00','present'),
(4,4,9,'2024-06-03','05:55:00','present'),
(5,5,7,'2024-06-04','19:10:00','retard'),
(6,6,12,'2024-06-03','07:55:00','present'),
(7,7,14,'2024-06-04','18:25:00','present'),
(8,8,15,'2024-06-05','20:10:00','retard'),
(9,1,2,'2024-06-05','19:35:00','present'),
(10,2,4,'2024-06-06','19:05:00','present');
/*!40000 ALTER TABLE `presences` ENABLE KEYS */;
UNLOCK TABLES;

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

--
-- Dumping data for table `produits`
--

LOCK TABLES `produits` WRITE;
/*!40000 ALTER TABLE `produits` DISABLE KEYS */;
INSERT INTO `produits` VALUES
(1,'HUI1851','Huile de massage relaxante',5000.00,50),
(2,'TAP7697','Tapis de Yoga Pro',12000.00,20),
(3,'PRO4933','Protège-tibias Arts Martiaux',8500.00,30),
(4,'BOI9576','Boisson énergétique protéinée',2500.00,100),
(5,'ÉLA5081','Élastique de résistance',4000.00,40);
/*!40000 ALTER TABLE `produits` ENABLE KEYS */;
UNLOCK TABLES;
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

-- Dump completed on 2026-08-08  1:11:01
