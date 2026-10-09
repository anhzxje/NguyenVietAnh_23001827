<?php
/**
 * File kết nối cơ sở dữ liệu MySQL bằng PDO
 * Bài tập tuần 4: Quản lý giỏ hàng
 * Sinh viên: Nguyễn Việt Anh - 23001827
 */

$host     = 'localhost';
$dbname   = 'shopping_cart';
$username = 'root';
$password = ''; // Mật khẩu mặc định trên XAMPP là rỗng

try {
    // Khởi tạo kết nối PDO
    $conn = new PDO("mysql:host={$host};dbname={$dbname};charset=utf8mb4", $username, $password, [
        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION, // Bật chế độ báo lỗi ngoại lệ
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,       // Mặc định trả về mảng kết hợp
        PDO::ATTR_EMULATE_PREPARES   => false,                  // Sử dụng native prepared statements
    ]);
} catch (PDOException $e) {
    // Xử lý trường hợp kết nối thất bại
    die("<div style='color: #ef4444; font-family: sans-serif; padding: 20px; background: #fee2e2; border-radius: 8px; margin: 20px;'>
            <h3>⚠️ Lỗi kết nối Database!</h3>
            <p>Không thể kết nối tới MySQL Database <strong>{$dbname}</strong>.</p>
            <p>Chi tiết lỗi: " . htmlspecialchars($e->getMessage()) . "</p>
         </div>");
}
