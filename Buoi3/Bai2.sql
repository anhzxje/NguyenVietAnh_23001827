-- Khởi tạo Database movie_management
CREATE DATABASE IF NOT EXISTS movie_management 
CHARACTER SET utf8mb4 
COLLATE utf8mb4_unicode_ci;

USE movie_management;

-- 1. Tạo bảng movies
-- Trường          | Kiểu dữ liệu   | Yêu cầu
-- id              | INT            | PRIMARY KEY, AUTO_INCREMENT
-- title           | VARCHAR(100)   | NOT NULL
-- price           | DECIMAL(10,2)  | NOT NULL
-- total_seats     | INT            | NOT NULL
-- available_seats | INT            | NOT NULL

DROP TABLE IF EXISTS movies;
CREATE TABLE movies (
    id INT PRIMARY KEY AUTO_INCREMENT,
    title VARCHAR(100) NOT NULL,
    price DECIMAL(10, 2) NOT NULL,
    total_seats INT NOT NULL,
    available_seats INT NOT NULL
);

-- 2. Thực hiện các yêu cầu

-- 2.1. Thêm ít nhất 5 bộ phim vào bảng
INSERT INTO movies (title, price, total_seats, available_seats) VALUES
('Avengers', 100000.00, 100, 80),
('Avatar', 120000.00, 80, 30),
('Batman', 90000.00, 120, 60),
('Spider-Man', 110000.00, 150, 75),
('Oppenheimer', 135000.00, 100, 20),
('Doraemon', 80000.00, 90, 55);

-- 2.2. Hiển thị toàn bộ danh sách phim
SELECT * FROM movies;

-- 2.3. Hiển thị phim có giá vé lớn hơn 100000
SELECT * FROM movies 
WHERE price > 100000;

-- 2.4. Hiển thị phim còn nhiều hơn 50 ghế
SELECT * FROM movies 
WHERE available_seats > 50;

-- 2.5. Sắp xếp phim theo giá vé giảm dần
SELECT * FROM movies 
ORDER BY price DESC;

-- 2.6. Cập nhật số ghế còn lại của một phim (Ví dụ: đặt thêm 10 vé phim 'Avengers', số ghế còn 70)
UPDATE movies 
SET available_seats = 70 
WHERE title = 'Avengers';

-- Kiểm tra lại sau khi cập nhật ghế:
SELECT * FROM movies WHERE title = 'Avengers';

-- 2.7. Xóa một phim (Ví dụ: Xóa phim 'Doraemon')
DELETE FROM movies 
WHERE title = 'Doraemon';

-- Kiểm tra lại danh sách phim sau khi xóa:
SELECT * FROM movies;

-- 2.8. Hiển thị số vé đã bán của từng phim: total_seats - available_seats
SELECT 
    id, 
    title, 
    total_seats, 
    available_seats, 
    (total_seats - available_seats) AS sold_seats
FROM movies;

-- 2.9. Tính doanh thu của từng phim: (total_seats - available_seats) × price
SELECT 
    id, 
    title, 
    price, 
    (total_seats - available_seats) AS sold_seats, 
    ((total_seats - available_seats) * price) AS revenue
FROM movies;

-- 2.10. Tính tổng doanh thu của tất cả các phim
SELECT 
    SUM((total_seats - available_seats) * price) AS total_revenue
FROM movies;

-- 2.11. Tìm phim có số vé bán ra nhiều nhất
-- Cách 1: Dùng subquery với hàm MAX (chuẩn theo yêu cầu từ khóa MAX của đề bài)
SELECT 
    id, 
    title, 
    total_seats, 
    available_seats, 
    (total_seats - available_seats) AS sold_seats
FROM movies
WHERE (total_seats - available_seats) = (
    SELECT MAX(total_seats - available_seats) FROM movies
);

-- Cách 2: Dùng ORDER BY ... DESC LIMIT 1
-- SELECT 
--     id, 
--     title, 
--     total_seats, 
--     available_seats, 
--     (total_seats - available_seats) AS sold_seats
-- FROM movies
-- ORDER BY (total_seats - available_seats) DESC
-- LIMIT 1;
