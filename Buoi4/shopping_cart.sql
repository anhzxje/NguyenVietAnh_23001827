-- =========================================================
-- BÀI TẬP TUẦN 4: QUẢN LÝ GIỎ HÀNG (SHOPPING CART)
-- Sinh viên: Nguyễn Việt Anh - MSV: 23001827
-- Cơ sở dữ liệu: shopping_cart
-- =========================================================

-- 1. Tạo database shopping_cart (nếu chưa có)
CREATE DATABASE IF NOT EXISTS `shopping_cart` 
CHARACTER SET utf8mb4 
COLLATE utf8mb4_unicode_ci;

USE `shopping_cart`;

-- 2. Xóa bảng cũ nếu tồn tại để tạo mới
DROP TABLE IF EXISTS `products`;

-- 3. Tạo bảng products theo đúng yêu cầu đề bài:
--    id: INT PRIMARY KEY, AUTO_INCREMENT
--    name: VARCHAR(100) NOT NULL
--    price: DECIMAL(10,2) NOT NULL
--    quantity: INT NOT NULL
CREATE TABLE `products` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `name` VARCHAR(100) NOT NULL,
    `price` DECIMAL(10, 2) NOT NULL,
    `quantity` INT NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 4. Thêm tối thiểu 5 sản phẩm mẫu
INSERT INTO `products` (`id`, `name`, `price`, `quantity`) VALUES
(1, 'iPhone 15 Pro Max 256GB', 29990000.00, 15),
(2, 'Samsung Galaxy S24 Ultra', 26990000.00, 20),
(3, 'MacBook Air M2 13.6 inch', 24500000.00, 12),
(4, 'Tai nghe Sony WH-1000XM5', 6990000.00, 30),
(5, 'Bàn phím cơ Logitech MX Mechanical', 3490000.00, 25),
(6, 'Chuột không dây Logitech MX Master 3S', 2190000.00, 40);
