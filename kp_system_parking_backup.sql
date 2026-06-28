-- MySQL dump 10.13  Distrib 9.5.0, for macos14.8 (arm64)
--
-- Host: localhost    Database: kp_system_parking
-- ------------------------------------------------------
-- Server version	9.5.0

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!50503 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;
SET @MYSQLDUMP_TEMP_LOG_BIN = @@SESSION.SQL_LOG_BIN;
SET @@SESSION.SQL_LOG_BIN= 0;

--
-- GTID state at the beginning of the backup 
--

SET @@GLOBAL.GTID_PURGED=/*!80000 '+'*/ '26a25f90-e9a7-11f0-8b05-099e0024b317:1-999';

--
-- Table structure for table `customers`
--

DROP TABLE IF EXISTS `customers`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `customers` (
  `id` int NOT NULL AUTO_INCREMENT,
  `nik` varchar(20) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `full_name` varchar(100) COLLATE utf8mb4_general_ci NOT NULL,
  `phone` varchar(20) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `email` varchar(100) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `address` text COLLATE utf8mb4_general_ci,
  `company` varchar(100) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime DEFAULT NULL,
  `created_by` int DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `customers`
--

LOCK TABLES `customers` WRITE;
/*!40000 ALTER TABLE `customers` DISABLE KEYS */;
INSERT INTO `customers` VALUES (5,'1111111111111111','Pandu sajiwa karyawan','111111111111','pandukaryawan@gmail.com','PT Cinta dia','PT Cinta dia','2025-12-02 00:39:11','2025-12-02 07:39:11',5),(6,'2222222222222222','Pandu sajiwa karyawan satu','222222222222','pandukaryawan1@gmail.com','PT alamat palsu','PT alamat palsu','2025-12-02 00:41:28','2025-12-02 07:41:28',9),(7,NULL,'Employee Unknown',NULL,NULL,NULL,'','2026-06-27 13:50:10',NULL,19),(8,NULL,'Test Employee',NULL,NULL,NULL,'Test Company','2026-06-27 21:23:35',NULL,21),(9,NULL,'Test Employee',NULL,NULL,NULL,'Test Company','2026-06-27 21:23:42',NULL,21);
/*!40000 ALTER TABLE `customers` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `migrations`
--

DROP TABLE IF EXISTS `migrations`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `migrations` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `version` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `class` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `group` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `namespace` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `time` int NOT NULL,
  `batch` int unsigned NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `migrations`
--

LOCK TABLES `migrations` WRITE;
/*!40000 ALTER TABLE `migrations` DISABLE KEYS */;
/*!40000 ALTER TABLE `migrations` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `parking_quota_management`
--

DROP TABLE IF EXISTS `parking_quota_management`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `parking_quota_management` (
  `id` int NOT NULL AUTO_INCREMENT,
  `month_year` varchar(7) COLLATE utf8mb4_general_ci NOT NULL,
  `total_quota` int NOT NULL,
  `used_quota` int DEFAULT '0',
  `created_by` int NOT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `created_by` (`created_by`),
  CONSTRAINT `parking_quota_management_ibfk_1` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `parking_quota_management`
--

LOCK TABLES `parking_quota_management` WRITE;
/*!40000 ALTER TABLE `parking_quota_management` DISABLE KEYS */;
INSERT INTO `parking_quota_management` VALUES (5,'2026-06',999999,23,7,'2026-06-27 16:13:26','2026-06-27 22:10:16');
/*!40000 ALTER TABLE `parking_quota_management` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `parking_submissions`
--

DROP TABLE IF EXISTS `parking_submissions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `parking_submissions` (
  `id` int NOT NULL AUTO_INCREMENT,
  `submission_code` varchar(20) COLLATE utf8mb4_general_ci NOT NULL,
  `customer_id` int NOT NULL,
  `vehicle_id` int NOT NULL,
  `submitted_by` int NOT NULL,
  `submission_date` date NOT NULL,
  `duration_days` int NOT NULL,
  `purpose` text COLLATE utf8mb4_general_ci NOT NULL,
  `id_card_image` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `vehicle_image` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `supporting_doc_image` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `operation_manager_approval` enum('pending','approved','rejected') COLLATE utf8mb4_general_ci DEFAULT 'pending',
  `operation_manager_id` int DEFAULT NULL,
  `operation_manager_notes` text COLLATE utf8mb4_general_ci,
  `operation_manager_approval_date` datetime DEFAULT NULL,
  `parking_dept_approval` enum('pending','approved','rejected') COLLATE utf8mb4_general_ci DEFAULT 'pending',
  `parking_dept_id` int DEFAULT NULL,
  `parking_dept_notes` text COLLATE utf8mb4_general_ci,
  `parking_dept_approval_date` datetime DEFAULT NULL,
  `quota_given` int DEFAULT NULL,
  `status` enum('draft','submitted','under_review','approved','rejected','completed') COLLATE utf8mb4_general_ci DEFAULT 'draft',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `submission_code` (`submission_code`),
  KEY `customer_id` (`customer_id`),
  KEY `vehicle_id` (`vehicle_id`),
  KEY `submitted_by` (`submitted_by`),
  KEY `operation_manager_id` (`operation_manager_id`),
  KEY `parking_dept_id` (`parking_dept_id`),
  CONSTRAINT `parking_submissions_ibfk_4` FOREIGN KEY (`operation_manager_id`) REFERENCES `users` (`id`),
  CONSTRAINT `parking_submissions_ibfk_5` FOREIGN KEY (`parking_dept_id`) REFERENCES `users` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=14 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `parking_submissions`
--

LOCK TABLES `parking_submissions` WRITE;
/*!40000 ALTER TABLE `parking_submissions` DISABLE KEYS */;
INSERT INTO `parking_submissions` VALUES (6,'FP202512023720',5,6,5,'2025-12-02',1,'buat meeting','1764661489_bca16010378d3438fbc2.jpg','1764661489_fa1de09cd3ba85cb0d78.jpg','1764661489_c98217b718af3bc009db.png','pending',NULL,NULL,NULL,'pending',NULL,NULL,NULL,NULL,'','2025-12-02 00:44:49','2026-06-27 04:49:30'),(7,'FP202512020125',5,6,5,'2025-12-02',1,'Untuk karaoke','1764661602_9c5cb406ed9b9bdb4268.jpg','1764661602_c2d00727ae5032401bb9.png','1764661602_a2ec60e6bdd190501dfe.png','approved',6,NULL,'2026-06-27 11:49:22','pending',NULL,NULL,NULL,NULL,'','2025-12-02 00:46:42','2026-06-27 06:24:50'),(8,'FP202512028263',5,6,5,'2025-12-02',1,'untuk ngeroom','1764661620_3550a9e14d6b4d61bbcb.jpg','1764661620_0a56cb179d02ee468092.png','1764661620_86b5c7c979075d05899f.png','rejected',6,'Kurang Tiket','2026-06-27 13:25:59','pending',NULL,NULL,NULL,NULL,'rejected','2025-12-02 00:47:00','2026-06-27 06:25:59'),(9,'FP202512051297',6,7,9,'2025-12-05',2,'dua hari meeting',NULL,NULL,NULL,'approved',6,NULL,'2026-06-27 13:25:43','pending',NULL,NULL,NULL,NULL,'','2025-12-04 22:02:30','2026-06-27 06:26:08'),(10,'FP202606272177',7,8,19,'2026-06-27',1,'Meeting',NULL,NULL,'1782568060_b7d4a2bbaed2c2ae38ce.png','approved',6,NULL,'2026-06-27 14:21:11','approved',7,NULL,'2026-06-27 16:13:26',1,'approved','2026-06-27 06:47:40','2026-06-27 09:13:26'),(11,'FP20260628042357',8,9,21,'2026-06-28',3,'Test submission',NULL,NULL,NULL,'pending',NULL,NULL,NULL,'pending',NULL,NULL,NULL,NULL,'submitted','2026-06-27 21:23:57','2026-06-27 21:23:57'),(12,'FP202606276235',8,10,21,'2026-06-27',4,'nn',NULL,NULL,'1782595511_4864e7239d4ff27bd619.png','pending',NULL,NULL,NULL,'approved',7,NULL,'2026-06-27 22:07:14',10,'approved','2026-06-27 14:25:11','2026-06-27 15:07:14'),(13,'FP202606278666',8,11,21,'2026-06-27',22,'Boss',NULL,NULL,'1782596515_2813b67620b4b6c60fa8.png','pending',NULL,NULL,NULL,'approved',7,NULL,'2026-06-27 22:10:16',12,'approved','2026-06-27 14:41:55','2026-06-27 15:10:16');
/*!40000 ALTER TABLE `parking_submissions` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `parking_usage`
--

DROP TABLE IF EXISTS `parking_usage`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `parking_usage` (
  `id` int NOT NULL AUTO_INCREMENT,
  `submission_id` int NOT NULL,
  `usage_date` date NOT NULL,
  `notes` text COLLATE utf8mb4_general_ci,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `submission_id` (`submission_id`),
  CONSTRAINT `parking_usage_ibfk_1` FOREIGN KEY (`submission_id`) REFERENCES `parking_submissions` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `parking_usage`
--

LOCK TABLES `parking_usage` WRITE;
/*!40000 ALTER TABLE `parking_usage` DISABLE KEYS */;
/*!40000 ALTER TABLE `parking_usage` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `users` (
  `id` int NOT NULL AUTO_INCREMENT,
  `username` varchar(50) COLLATE utf8mb4_general_ci NOT NULL,
  `password` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `full_name` varchar(100) COLLATE utf8mb4_general_ci NOT NULL,
  `email` varchar(100) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `role` enum('admin','employee','operation_manager','parking_dept') COLLATE utf8mb4_general_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `is_active` tinyint(1) DEFAULT '1',
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `username` (`username`)
) ENGINE=InnoDB AUTO_INCREMENT=22 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `users`
--

LOCK TABLES `users` WRITE;
/*!40000 ALTER TABLE `users` DISABLE KEYS */;
INSERT INTO `users` VALUES (5,'pandukaryawan','$2y$10$r9kmfjYtz3zqb6TFuCXYiuYEmt3L8pVXquCYmW2h3RuyWaHSUkbmy','Sajiwa P.','pandukaryawan@gmail.com','employee','2025-11-30 09:15:05',1,'2025-11-30 16:15:05'),(6,'pandumanager','$2y$10$BQi0a8ynyss5NC81oKuR9.It2zD1O3KhT03lJEGVN904eLhKyoRB2','Manager','pandumanager@gmail.co','operation_manager','2025-11-30 09:15:39',1,'2025-11-30 16:15:39'),(7,'pandudept','$2y$10$xh26DWxfCIb0M9xcz4Qmw.We4cUBcX8X9N03J8IPwwZgqDJj3PWie','Pandu S.','r','parking_dept','2025-11-30 09:15:56',1,'2025-11-30 16:15:56'),(9,'pandukaryawan1','$2y$10$KdftLbooBskHsF3KmUkAHu9qPMiqa6u9VQ4mPSXtmuQS3gqF1YU4q','pandukaryawan1','pandukaryawan1@gmail.com','employee','2025-12-02 00:29:24',1,'2025-12-02 07:29:24'),(14,'pandu_mgr','$2y$10$Bf7Bq8qP4z9dCzAMC/IFg.mz99CX1ZedyUQsetNGGm7X27tORV9IO%','Manager Full Name','manager@example.com','operation_manager','2026-06-27 11:42:36',1,NULL),(16,'pandu_mngr','$2y$10$Bf7Bq8qP4z9dCzAMC/IFg.mz99CX1ZedyUQsetNGGm7X27tORV9IO%','Manager Full Name','pandu_mgr@example.com','operation_manager','2026-06-27 11:43:00',1,NULL),(18,'riki_emp','$2y$10$xWW4NC3n.LVD7axMY.KqROhqndhQqyA/NdHmccQ2WIt8JuX46rfSq','Riki Jc','riki@mailsac.com','employee','2026-06-27 04:51:13',1,'2026-06-27 11:51:13'),(19,'fiqinasrullah','$2y$10$S2ryT/Wh4VEPb8IYjWYfy.FgiqiQ6l3jaUbBmMSR.gVnpWiU.vFPK','Fiqi Nasrullah','fiqi@arealestat.com','employee','2026-06-27 06:29:26',1,'2026-06-27 13:29:26'),(21,'test_employee','$2y$10$190gpVTZxkB6h3PhnxDILO6ZYgqy4D4DpdjokDyzn73fd0MlEkAJu','Test Employee','test@employee.com','employee','2026-06-27 21:05:41',1,NULL);
/*!40000 ALTER TABLE `users` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `vehicles`
--

DROP TABLE IF EXISTS `vehicles`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `vehicles` (
  `id` int NOT NULL AUTO_INCREMENT,
  `license_plate` varchar(20) COLLATE utf8mb4_general_ci NOT NULL,
  `vehicle_type` enum('car','motorcycle','truck') COLLATE utf8mb4_general_ci NOT NULL,
  `brand` varchar(50) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `model` varchar(50) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `color` varchar(30) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `created_by` int DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_plate_per_user` (`license_plate`,`created_by`)
) ENGINE=InnoDB AUTO_INCREMENT=12 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `vehicles`
--

LOCK TABLES `vehicles` WRITE;
/*!40000 ALTER TABLE `vehicles` DISABLE KEYS */;
INSERT INTO `vehicles` VALUES (6,'AE69EK','car','Mercedes Bens 69 Keren','Jeep','Pink',5),(7,'B3RAK','car','CRV','SUV','Merah',9),(8,'B 4 BI','motorcycle','Yamaha','Xeon','Putih',19),(9,'T 1 TE','car','Toyota','Avanza','Silver',21),(10,'Z 123 JJJ','car','Honda','Brio','Kuning',21),(11,'P 99 TGS','motorcycle','Yamaha','Aerok','Kuning',21);
/*!40000 ALTER TABLE `vehicles` ENABLE KEYS */;
UNLOCK TABLES;
SET @@SESSION.SQL_LOG_BIN = @MYSQLDUMP_TEMP_LOG_BIN;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2026-06-28 13:08:21
