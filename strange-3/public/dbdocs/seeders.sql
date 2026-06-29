-- MySQL Database Seeders (Sample Data) for cpad_03_strange

-- Insert into menus
INSERT INTO `menus` VALUES 
('D001','Jus Oren Ice','Sweet and tangy fresh orange juice blended to perfection and served ice cold',5.90,'drink',1),
('D002','Jus Carrot Ice','Freshly pressed carrot juice with a naturally earthy sweetness, served chilled',4.90,'drink',1),
('D003','Jus Carrot Susu Ice','Creamy carrot juice blended with fresh milk for a smooth and refreshing taste',5.40,'drink',1),
('D004','Jus Tembikai Ice','Juicy watermelon blended into a light and refreshing drink, served icy cold',5.90,'drink',1),
('D005','Jus Tembikai Susu Ice','Chilled watermelon juice blended with fresh milk for a creamy tropical cooldown',6.40,'drink',1),
('D006','Jus Epal Ice','Crisp and subtly sweet apple juice served cold, the perfect refreshing sip',5.90,'drink',1),
('F001','Ayam Geprek Sambal Merah','Crispy fried chicken smashed and coated in a fiery red chili sambal that hits hard',11.20,'food',1),
('F002','Ayam Geprek Sambal Hijau','Crispy smashed chicken topped with a fragrant and mildly spicy green chili sambal',11.20,'food',1),
('F003','Ayam Geprek Sambal Brown Sugar','Crispy smashed chicken with a bold sambal that balances smoky heat and caramel sweetness',11.20,'food',1),
('F004','Ayam Geprek Sambal Harimau','Crispy smashed chicken loaded with fresh sliced chilies for a raw and punchy heat',11.20,'food',1),
('F005','Ayam Geprek Sambal Bawean','Crispy smashed chicken with a deep and complex shrimp paste sambal from the Bawean tradition',11.20,'food',1),
('F006','Ayam Geprek Sambal 2 Rasa','Crispy smashed chicken served with two sambal choices so you get the best of both worlds',11.70,'food',1),
('F007','Ayam Geprek Sambal 3 Rasa','Crispy smashed chicken served with three sambal choices for the ultimate sambal experience',12.20,'food',1)
ON DUPLICATE KEY UPDATE `name`=VALUES(`name`);

-- Insert into users
INSERT INTO `users` (`user_id`, `name`, `password`, `role`, `phone`, `address`) VALUES 
('A001','admin','admin123','admin','0137461935','Vendor counter'),
('A002','vendor','vendor123','admin','0158163956','Kitchen counter'),
('C001','customer','customer123','customer','0157164916','Kolej Tun Razak, UTM'),
('C002','muhammad','abcdefg','customer','0183472383','Kolej Rahman Putra, UTM'),
('C003','danial','ABCDEFG','customer','0147153852','Kolej Tun Hussein Onn, UTM'),
('C004','afiq','1234567','customer','0183649261','Kolej Perdana, UTM'),
('C005','siang','siangmalam','customer','0117461835','Kolej 9, UTM'),
('C006','ng','NotGood','customer','0147528251','Kolej 10, UTM'),
('C007','wei','24681357','customer','0192715418','Kolej Datin Seri Endon, UTM')
ON DUPLICATE KEY UPDATE `name`=VALUES(`name`), `phone`=VALUES(`phone`), `address`=VALUES(`address`);

-- Insert into orders
INSERT INTO `orders` (`order_id`, `user_id`, `total_amount`, `status`, `delivery_method`, `payment_method`, `order_date`) VALUES 
('O001','C001',11.20,'completed','pickup','cash','2026-05-13 12:25:30'),
('O002','C002',17.10,'completed','delivery','credit_card','2026-05-13 12:25:30'),
('O003','C003',22.40,'completed','pickup','ewallet','2026-05-13 12:25:30'),
('O004','C004',16.10,'completed','delivery','cash','2026-05-13 12:25:30'),
('O005','C005',16.60,'completed','pickup','credit_card','2026-05-13 12:25:30'),
('O006','C006',17.60,'completed','delivery','ewallet','2026-05-13 12:25:30'),
('O007','C007',18.60,'completed','pickup','cash','2026-05-13 12:25:30'),
('O008','C001',22.40,'completed','delivery','credit_card','2026-05-13 12:25:30'),
('O009','C002',23.00,'pending','pickup','ewallet','2026-05-13 12:25:30'),
('O010','C003',32.20,'pending','delivery','cash','2026-05-13 12:25:30'),
('O011','C004',11.20,'pending','pickup','credit_card','2026-05-13 12:25:30'),
('O012','C005',28.80,'pending','delivery','ewallet','2026-05-13 12:25:30'),
('O013','C006',24.00,'pending','pickup','cash','2026-05-13 12:25:30'),
('O014','C007',19.20,'pending','delivery','credit_card','2026-05-13 12:25:30'),
('O015','C001',22.00,'pending','pickup','ewallet','2026-05-13 12:25:30'),
('O016','C002',22.40,'pending','delivery','cash','2026-05-13 12:25:30'),
('O017','C006',18.10,'pending','pickup','credit_card','2026-05-13 12:25:30'),
('O018','C007',24.40,'pending','delivery','ewallet','2026-05-13 12:25:30')
ON DUPLICATE KEY UPDATE `status`=VALUES(`status`), `delivery_method`=VALUES(`delivery_method`), `payment_method`=VALUES(`payment_method`);

-- Insert into order_items
INSERT INTO `order_items` VALUES 
('OI001','O001','F001',1,11.20),
('OI002','O002','F002',1,11.20),
('OI003','O002','D001',1,5.90),
('OI004','O003','F003',2,22.40),
('OI005','O004','F004',1,11.20),
('OI006','O004','D002',1,4.90),
('OI007','O005','F005',1,11.20),
('OI008','O005','D003',1,5.40),
('OI009','O006','F006',1,11.70),
('OI010','O006','D004',1,5.90),
('OI011','O007','F007',1,12.20),
('OI012','O007','D005',1,6.40),
('OI013','O008','F001',1,11.20),
('OI014','O008','F002',1,11.20),
('OI015','O009','F003',1,11.20),
('OI016','O009','D001',2,11.80),
('OI017','O010','F004',2,22.40),
('OI018','O010','D002',2,9.80),
('OI019','O011','F005',1,11.20),
('OI020','O012','F006',2,23.40),
('OI021','O012','D003',1,5.40),
('OI022','O013','F007',1,12.20),
('OI023','O013','D004',2,11.80),
('OI024','O014','D005',3,19.20),
('OI025','O015','F001',1,11.20),
('OI026','O015','D001',1,5.90),
('OI027','O015','D002',1,4.90),
('OI028','O016','F002',1,11.20),
('OI029','O016','F003',1,11.20),
('OI030','O017','F006',1,11.70),
('OI031','O017','D005',1,6.40),
('OI032','O018','F007',2,24.40)
ON DUPLICATE KEY UPDATE `quantity`=VALUES(`quantity`);
