-- MySQL Database Seeders (Sample Data) for cpad_03_strange

-- Insert into menus
INSERT INTO `menus` VALUES 
('D001','Jus Oren Ice','Chilled orange juice',5.90,'drink',1),
('D002','Jus Carrot Ice','freshly squeezed carrot juice',4.90,'drink',1),
('D003','Jus Carrot Susu Ice','Chilled carrot juice with milk',5.40,'drink',1),
('D004','Jus Tembikai Ice','Chilled watermelon juice',5.90,'drink',1),
('D005','Jus Tembikai Susu Ice','Chilled watermelon juice with milk',6.40,'drink',1),
('F001','Ayam Geprek Sambal Merah','Ayam geprek with spicy red hot chili',11.20,'food',1),
('F002','Ayam Geprek Sambal Hijau','Ayam geprek with mild-spicy green chili',11.20,'food',1),
('F003','Ayam Geprek Sambal Brown Sugar','Ayam geprek with sweet & spicy taste',11.20,'food',1),
('F004','Ayam Geprek Sambal Harimau','Ayam geprek with sliced chili topping',11.20,'food',1),
('F005','Ayam Geprek Sambal Bawean','Ayam Geprek with authentic Indonesian shrimp paste sambal',11.20,'food',1),
('F006','Ayam Geprek Sambal 2 Rasa','Ayam Geprek with 2 choices of sambal',11.70,'food',1),
('F007','Ayam Geprek Sambal 3 Rasa','Ayam Geprek with 3 choices of sambal',12.20,'food',1)
ON DUPLICATE KEY UPDATE `name`=VALUES(`name`);

-- Insert into users
INSERT INTO `users` VALUES 
('A001','Admin','admin123','admin','0137461935'),
('A002','Vendor','vendor123','admin','0158163956'),
('C001','Customer','customer123','customer','0157164916'),
('C002','Muhammad','abcdefg','customer','0183472383'),
('C003','Danial','ABCDEFG','customer','0147153852'),
('C004','Afiq','1234567','customer','0183649261'),
('C005','Siang','siangmalam','customer','0117461835'),
('C006','Ng','NotGood','customer','0147528251'),
('C007','Wei','24681357','customer','0192715418')
ON DUPLICATE KEY UPDATE `name`=VALUES(`name`);

-- Insert into orders
INSERT INTO `orders` VALUES 
('O001','C001',11.20,'completed','2026-05-13 12:25:30'),
('O002','C002',17.10,'completed','2026-05-13 12:25:30'),
('O003','C003',22.40,'completed','2026-05-13 12:25:30'),
('O004','C004',16.10,'completed','2026-05-13 12:25:30'),
('O005','C005',16.60,'completed','2026-05-13 12:25:30'),
('O006','C006',17.60,'completed','2026-05-13 12:25:30'),
('O007','C007',18.60,'completed','2026-05-13 12:25:30'),
('O008','C001',22.40,'completed','2026-05-13 12:25:30'),
('O009','C002',23.00,'pending','2026-05-13 12:25:30'),
('O010','C003',32.20,'pending','2026-05-13 12:25:30'),
('O011','C004',11.20,'pending','2026-05-13 12:25:30'),
('O012','C005',28.80,'pending','2026-05-13 12:25:30'),
('O013','C006',24.00,'pending','2026-05-13 12:25:30'),
('O014','C007',19.20,'pending','2026-05-13 12:25:30'),
('O015','C001',22.00,'pending','2026-05-13 12:25:30'),
('O016','C002',22.40,'pending','2026-05-13 12:25:30'),
('O017','C006',18.10,'pending','2026-05-13 12:25:30'),
('O018','C007',24.40,'pending','2026-05-13 12:25:30')
ON DUPLICATE KEY UPDATE `status`=VALUES(`status`);

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
