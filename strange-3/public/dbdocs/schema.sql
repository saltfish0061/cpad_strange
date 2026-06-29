-- MySQL Database Schema for cpad_03_strange

CREATE TABLE IF NOT EXISTS `menus` (
  `item_id` varchar(20) NOT NULL,
  `name` varchar(50) NOT NULL,
  `description` text DEFAULT NULL,
  `price` decimal(10,2) NOT NULL,
  `category` enum('food','drink') NOT NULL,
  `is_available` tinyint(1) DEFAULT 1,
  PRIMARY KEY (`item_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE IF NOT EXISTS `users` ( 
  `user_id` varchar(20) NOT NULL, 
  `name` varchar(50) NOT NULL, 
  `password` varchar(255) NOT NULL DEFAULT 'temporary_default_password',
  `role` enum('admin','customer') NOT NULL DEFAULT 'customer', 
  `phone` varchar(20) DEFAULT NULL, 
  `address` text DEFAULT NULL, 
  PRIMARY KEY (`user_id`), 
  UNIQUE KEY `users_name_unique` (`name`) 
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE IF NOT EXISTS `orders` (
  `order_id` varchar(20) NOT NULL,
  `user_id` varchar(20) NOT NULL,
  `total_amount` decimal(10,2) NOT NULL,
  `status` enum('pending','preparing','ready','on_the_way','completed','cancelled') DEFAULT 'pending',
  `delivery_method` enum('pickup','delivery') NOT NULL DEFAULT 'pickup',
  `payment_method` enum('cash','credit_card','ewallet') NOT NULL DEFAULT 'cash',
  `order_note` text DEFAULT NULL,
  `order_date` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`order_id`),
  KEY `user_id` (`user_id`),
  CONSTRAINT `orders_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE IF NOT EXISTS `order_items` (
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
