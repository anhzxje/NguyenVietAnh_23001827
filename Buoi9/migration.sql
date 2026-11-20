SET NAMES utf8mb4;
-- Tùy chọn: chạy MỘT LẦN trong database CRUD cũ đã chọn.
-- Không cần file này khi đã dùng database.sql để cài mới.
ALTER TABLE products ADD COLUMN image VARCHAR(255) NULL DEFAULT NULL;
-- Bổ sung đủ ít nhất 12 sản phẩm, giữ nguyên dữ liệu cũ.
INSERT INTO products (name, price, stock)
SELECT CONCAT('Sản phẩm mẫu ', seed.n), 10000, 10
FROM (
    SELECT 1 n UNION ALL SELECT 2 UNION ALL SELECT 3 UNION ALL SELECT 4
    UNION ALL SELECT 5 UNION ALL SELECT 6 UNION ALL SELECT 7 UNION ALL SELECT 8
    UNION ALL SELECT 9 UNION ALL SELECT 10 UNION ALL SELECT 11 UNION ALL SELECT 12
) seed
CROSS JOIN (SELECT COUNT(*) total FROM products) existing
WHERE seed.n <= GREATEST(0, 12 - existing.total);
