CREATE DATABASE IF NOT EXISTS bilet_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE bilet_db;

CREATE TABLE IF NOT EXISTS users (
  id INT AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(100) NOT NULL,
  email VARCHAR(150) NOT NULL UNIQUE,
  password VARCHAR(255) NOT NULL,
  role ENUM('user','admin','firma_admin') NOT NULL DEFAULT 'user',
  balance DECIMAL(10,2) NOT NULL DEFAULT 0,
  firm_id INT NULL
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS firms (
  id INT AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(120) NOT NULL,
  description TEXT
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS seferler (
  id INT AUTO_INCREMENT PRIMARY KEY,
  firm_id INT NOT NULL,
  from_city VARCHAR(100) NOT NULL,
  to_city VARCHAR(100) NOT NULL,
  date DATE NOT NULL,
  time TIME NOT NULL,
  price DECIMAL(10,2) NOT NULL,
  total_seats INT NOT NULL DEFAULT 40,
  FOREIGN KEY (firm_id) REFERENCES firms(id) ON DELETE CASCADE
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS coupons (
  id INT AUTO_INCREMENT PRIMARY KEY,
  code VARCHAR(50) NOT NULL UNIQUE,
  discount_rate INT NOT NULL,
  usage_limit INT NOT NULL,
  firm_id INT NULL,
  valid_until DATE NOT NULL,
  FOREIGN KEY (firm_id) REFERENCES firms(id) ON DELETE SET NULL
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS biletler (
  id INT AUTO_INCREMENT PRIMARY KEY,
  user_id INT NOT NULL,
  sefer_id INT NOT NULL,
  seat_no INT NOT NULL,
  price_paid DECIMAL(10,2) NOT NULL,
  coupon_code VARCHAR(50) NULL,
  status ENUM('aktif','iptal') NOT NULL DEFAULT 'aktif',
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
  FOREIGN KEY (sefer_id) REFERENCES seferler(id) ON DELETE CASCADE
) ENGINE=InnoDB;

INSERT INTO firms (name, description) VALUES
('Metre Turizm', 'Konforlu şehirlerarası yolculuk'),
('Anka Seyahat', 'Türkiye genelinde hızlı ulaşım');

INSERT INTO users (name, email, password, role, balance, firm_id) VALUES
('Sistem Yöneticisi', 'admin@site.com', '123456', 'admin', 0, NULL),
('Metre Firma Admin', 'firma@site.com', '123456', 'firma_admin', 0, 1),
('Test Kullanıcı', 'user@site.com', '123456', 'user', 500, NULL);

INSERT INTO seferler (firm_id, from_city, to_city, date, time, price, total_seats) VALUES
(1, 'İstanbul', 'Ankara', DATE_ADD(CURDATE(), INTERVAL 1 DAY), '10:00:00', 450.00, 40),
(1, 'Ankara', 'İzmir', DATE_ADD(CURDATE(), INTERVAL 2 DAY), '13:30:00', 520.00, 40),
(2, 'İzmir', 'İstanbul', DATE_ADD(CURDATE(), INTERVAL 3 DAY), '09:00:00', 480.00, 40);

INSERT INTO coupons (code, discount_rate, usage_limit, firm_id, valid_until) VALUES
('GLOBAL10', 10, 100, NULL, DATE_ADD(CURDATE(), INTERVAL 60 DAY)),
('TEK50', 20, 50, 1, DATE_ADD(CURDATE(), INTERVAL 60 DAY));

ALTER TABLE biletler ADD COLUMN canceled_at DATETIME NULL;
