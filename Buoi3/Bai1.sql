-- Khởi tạo Database shopping_cart
CREATE DATABASE IF NOT EXISTS shopping_cart 
CHARACTER SET utf8mb4 
COLLATE utf8mb4_unicode_ci;

USE shopping_cart;

-- 1. Tạo bảng cart_items
-- Trường          | Kiểu dữ liệu   | Yêu cầu
-- id              | INT            | PRIMARY KEY, AUTO_INCREMENT
-- name            | VARCHAR(100)   | NOT NULL
-- price           | DECIMAL(10,2)  | NOT NULL
-- quantity        | INT            | NOT NULL

DROP TABLE IF EXISTS cart_items;
CREATE TABLE cart_items (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(100) NOT NULL,
    price DECIMAL(10, 2) NOT NULL,
    quantity INT NOT NULL
);

-- 2. Thực hiện các yêu cầu

-- 2.1. Thêm ít nhất 5 sản phẩm vào bảng
INSERT INTO cart_items (name, price, quantity) VALUES
('Áo thun', 150000.00, 2),
('Quần jean', 350000.00, 1),
('Giày thể thao', 500000.00, 1),
('Nón lưỡi trai', 80000.00, 3),
('Áo khoác gió', 250000.00, 6),
('Tất cổ ngắn', 45000.00, 10);

-- 2.2. Hiển thị toàn bộ sản phẩm
SELECT * FROM cart_items;

-- 2.3. Hiển thị sản phẩm có giá lớn hơn 100000
SELECT * FROM cart_items 
WHERE price > 100000;

-- 2.4. Hiển thị sản phẩm có số lượng lớn hơn 5
SELECT * FROM cart_items 
WHERE quantity > 5;

-- 2.5. Sắp xếp sản phẩm theo giá giảm dần
SELECT * FROM cart_items 
ORDER BY price DESC;

-- 2.6. Cập nhật giá của một sản phẩm (Cập nhật giá 'Áo thun' thành 180000)
UPDATE cart_items 
SET price = 180000.00 
WHERE name = 'Áo thun';

-- Kiểm tra lại sau khi cập nhật giá:
SELECT * FROM cart_items WHERE name = 'Áo thun';

-- 2.7. Cập nhật số lượng của một sản phẩm (Cập nhật số lượng 'Nón lưỡi trai' thành 5)
UPDATE cart_items 
SET quantity = 5 
WHERE name = 'Nón lưỡi trai';

-- Kiểm tra lại sau khi cập nhật số lượng:
SELECT * FROM cart_items WHERE name = 'Nón lưỡi trai';

-- 2.8. Xóa một sản phẩm (Xóa sản phẩm 'Quần jean')
DELETE FROM cart_items 
WHERE name = 'Quần jean';

-- Kiểm tra lại bảng sau khi xóa:
SELECT * FROM cart_items;

-- 2.9. Hiển thị tên sản phẩm, giá, số lượng và thành tiền (price × quantity)
SELECT 
    name, 
    price, 
    quantity, 
    (price * quantity) AS total_amount
FROM cart_items;

-- 2.10. Tính tổng tiền của toàn bộ giỏ hàng
SELECT 
    SUM(price * quantity) AS grand_total
FROM cart_items;
