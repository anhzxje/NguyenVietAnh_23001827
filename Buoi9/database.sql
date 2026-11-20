SET NAMES utf8mb4;
-- Cài mới Buổi 9, độc lập với database product_management của Buổi 8.
CREATE DATABASE IF NOT EXISTS buoi9_product_management
    CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE buoi9_product_management;
-- Không xóa bảng đang có. Dùng migration.sql nếu tiếp tục database cũ.
CREATE TABLE products (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    price INT NOT NULL CHECK (price > 0),
    stock INT NOT NULL CHECK (stock >= 0),
    image VARCHAR(255) NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
INSERT INTO products (name, price, stock) VALUES
('Bút bi', 5000, 20),
('Vở ô ly', 12000, 15),
('Thước kẻ', 8000, 0),
('Bút chì', 6000, 30),
('Tẩy gôm', 4000, 25),
('Hộp bút', 25000, 10),
('Bút màu', 45000, 12),
('Sổ tay', 35000, 18),
('Giấy A4', 65000, 8),
('Kéo học sinh', 15000, 14),
('Hồ dán', 7000, 22),
('Bìa hồ sơ', 3000, 40);
