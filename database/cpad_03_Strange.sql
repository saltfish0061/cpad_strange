-- MariaDB dump 10.19  Distrib 10.4.32-MariaDB, for Win64 (AMD64)
--
-- Host: localhost    Database: cpad_03_strange
-- ------------------------------------------------------
-- Server version	10.4.32-MariaDB

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
-- Table structure for table `menus`
--

DROP TABLE IF EXISTS `menus`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `menus` (
  `item_id` varchar(20) NOT NULL,
  `name` varchar(50) NOT NULL,
  `description` text DEFAULT NULL,
  `price` decimal(10,2) NOT NULL,
  `category` enum('food','drink') NOT NULL,
  `is_available` tinyint(1) DEFAULT 1,
  PRIMARY KEY (`item_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `menus`
--

LOCK TABLES `menus` WRITE;
/*!40000 ALTER TABLE `menus` DISABLE KEYS */;
INSERT INTO `menus` VALUES ('D001','Jus Oren Ice','Chilled orange juice',5.90,'drink',1),('D002','Jus Carrot Ice','freshly squeezed carrot juice',4.90,'drink',1),('D003','Jus Carrot Susu Ice','Chilled carrot juice with milk',5.40,'drink',1),('D004','Jus Tembikai Ice','Chilled watermelon juice',5.90,'drink',1),('D005','Jus Tembikai Susu Ice','Chilled watermelon juice with milk',6.40,'drink',1),('F001','Ayam Geprek Sambal Merah','Ayam geprek with spicy red hot chili',11.20,'food',1),('F002','Ayam Geprek Sambal Hijau','Ayam geprek with mild-spicy green chili',11.20,'food',1),('F003','Ayam Geprek Sambal Brown Sugar','Ayam geprek with sweet & spicy taste',11.20,'food',1),('F004','Ayam Geprek Sambal Harimau','Ayam geprek with sliced chili topping',11.20,'food',1),('F005','Ayam Geprek Sambal Bawean','Ayam Geprek with authentic Indonesian shrimp paste sambal',11.20,'food',1),('F006','Ayam Geprek Sambal 2 Rasa','Ayam Geprek with 2 choices of sambal',11.70,'food',1),('F007','Ayam Geprek Sambal 3 Rasa','Ayam Geprek with 3 choices of sambal',12.20,'food',1);
/*!40000 ALTER TABLE `menus` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `order_items`
--

DROP TABLE IF EXISTS `order_items`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `order_items` (
  `order_item_id` varchar(20) NOT NULL,
  `order_id` varchar(20) NOT NULL,
  `item_id` varchar(20) NOT NULL,
  `quantity` int(11) NOT NULL DEFAULT 1,
  `subtotal` decimal(10,2) NOT NULL,
  PRIMARY KEY (`order_item_id`),
  KEY `order_id` (`order_id`),
  KEY `item_id` (`item_id`),
  CONSTRAINT `order_items_ibfk_1` FOREIGN KEY (`order_id`) REFERENCES `orders` (`order_id`) ON DELETE CASCADE,
  CONSTRAINT `order_items_ibfk_2` FOREIGN KEY (`item_id`) REFERENCES `menus` (`item_id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `order_items`
--

LOCK TABLES `order_items` WRITE;
/*!40000 ALTER TABLE `order_items` DISABLE KEYS */;
INSERT INTO `order_items` VALUES ('OI001','O001','F001',1,11.20),('OI002','O002','F002',1,11.20),('OI003','O002','D001',1,5.90),('OI004','O003','F003',2,22.40),('OI005','O004','F004',1,11.20),('OI006','O004','D002',1,4.90),('OI007','O005','F005',1,11.20),('OI008','O005','D003',1,5.40),('OI009','O006','F006',1,11.70),('OI010','O006','D004',1,5.90),('OI011','O007','F007',1,12.20),('OI012','O007','D005',1,6.40),('OI013','O008','F001',1,11.20),('OI014','O008','F002',1,11.20),('OI015','O009','F003',1,11.20),('OI016','O009','D001',2,11.80),('OI017','O010','F004',2,22.40),('OI018','O010','D002',2,9.80),('OI019','O011','F005',1,11.20),('OI020','O012','F006',2,23.40),('OI021','O012','D003',1,5.40),('OI022','O013','F007',1,12.20),('OI023','O013','D004',2,11.80),('OI024','O014','D005',3,19.20),('OI025','O015','F001',1,11.20),('OI026','O015','D001',1,5.90),('OI027','O015','D002',1,4.90),('OI028','O016','F002',1,11.20),('OI029','O016','F003',1,11.20),('OI030','O017','F006',1,11.70),('OI031','O017','D005',1,6.40),('OI032','O018','F007',2,24.40);
/*!40000 ALTER TABLE `order_items` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `orders`
--

DROP TABLE IF EXISTS `orders`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `orders` (
  `order_id` varchar(20) NOT NULL,
  `user_id` varchar(20) NOT NULL,
  `total_amount` decimal(10,2) NOT NULL,
  `status` enum('pending','preparing','ready','completed','cancelled') DEFAULT 'pending',
  `order_date` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`order_id`),
  KEY `user_id` (`user_id`),
  CONSTRAINT `orders_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `orders`
--

LOCK TABLES `orders` WRITE;
/*!40000 ALTER TABLE `orders` DISABLE KEYS */;
INSERT INTO `orders` VALUES ('O001','C001',11.20,'completed','2026-05-13 12:25:30'),('O002','C002',17.10,'completed','2026-05-13 12:25:30'),('O003','C003',22.40,'completed','2026-05-13 12:25:30'),('O004','C004',16.10,'completed','2026-05-13 12:25:30'),('O005','C005',16.60,'completed','2026-05-13 12:25:30'),('O006','C006',17.60,'completed','2026-05-13 12:25:30'),('O007','C007',18.60,'completed','2026-05-13 12:25:30'),('O008','C001',22.40,'completed','2026-05-13 12:25:30'),('O009','C002',23.00,'pending','2026-05-13 12:25:30'),('O010','C003',32.20,'pending','2026-05-13 12:25:30'),('O011','C004',11.20,'pending','2026-05-13 12:25:30'),('O012','C005',28.80,'pending','2026-05-13 12:25:30'),('O013','C006',24.00,'pending','2026-05-13 12:25:30'),('O014','C007',19.20,'pending','2026-05-13 12:25:30'),('O015','C001',22.00,'pending','2026-05-13 12:25:30'),('O016','C002',22.40,'pending','2026-05-13 12:25:30'),('O017','C006',18.10,'pending','2026-05-13 12:25:30'),('O018','C007',24.40,'pending','2026-05-13 12:25:30');
/*!40000 ALTER TABLE `orders` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `users` (
  `user_id` varchar(20) NOT NULL,
  `name` varchar(50) NOT NULL,
  `password` varchar(255) DEFAULT NULL,
  `role` enum('admin','customer') NOT NULL DEFAULT 'customer',
  `phone` varchar(20) DEFAULT NULL,
  PRIMARY KEY (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `users`
--

LOCK TABLES `users` WRITE;
/*!40000 ALTER TABLE `users` DISABLE KEYS */;
INSERT INTO `users` VALUES ('A001','Admin_Geprek','Geprek4ever','admin','0137461935'),('A002','Admin_Sambal','Sambal4ever','admin','0158163956'),('C001','Saltfish','wwww2333','customer','0157164916'),('C002','Muhammad','abcdefg','customer','0183472383'),('C003','Danial','ABCDEFG','customer','0147153852'),('C004','Afiq','1234567','customer','0183649261'),('C005','Siang','siangmalam','customer','0117461835'),('C006','Ng','NotGood','customer','0147528251'),('C007','Wei','24681357','customer','0192715418');
/*!40000 ALTER TABLE `users` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2026-05-15 13:41:12
