-- ============================================================
-- CÂU 1: TẠO CƠ SỞ DỮ LIỆU VÀ BẢNG PRODUCTS
-- Bài thực hành: Quản lý sản phẩm (PHP & Vue.js)
-- Sinh viên: Nguyễn Việt Anh - MSV: 23001827
-- ============================================================

-- 1. Tạo cơ sở dữ liệu nếu chưa tồn tại
CREATE DATABASE IF NOT EXISTS `product_management`
    CHARACTER SET utf8mb4
    COLLATE utf8mb4_unicode_ci;

USE `product_management`;

-- 2. Xóa bảng cũ nếu đã tồn tại để tạo mới sạch sẽ
DROP TABLE IF EXISTS `products`;

-- 3. Tạo bảng products theo cấu trúc yêu cầu
CREATE TABLE `products` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `name` VARCHAR(100) NOT NULL COMMENT 'Tên sản phẩm, bắt buộc nhập, tối đa 100 ký tự',
    `price` INT NOT NULL COMMENT 'Đơn giá tính bằng đồng, số nguyên lớn hơn 0',
    `stock` INT NOT NULL COMMENT 'Số lượng tồn, số nguyên không âm',
    CONSTRAINT `chk_products_price` CHECK (`price` > 0),
    CONSTRAINT `chk_products_stock` CHECK (`stock` >= 0)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 4. Chèn 3 dòng dữ liệu mẫu
INSERT INTO `products` (`name`, `price`, `stock`) VALUES
('Bút bi', 5000, 20),
('Vở ô ly', 12000, 15),
('Thước kẻ', 8000, 0);

