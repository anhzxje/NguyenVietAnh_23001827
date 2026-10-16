<?php
/**
 * File kết nối cơ sở dữ liệu MySQL bằng PDO
 * Bài tập tuần 5: Quản lý giỏ hàng (Shopping Cart)
 * Sinh viên: Nguyễn Việt Anh - MSV: 23001827
 */

$host     = '127.0.0.1'; // hoặc 'localhost'
$dbname   = 'shopping_cart';
$username = 'root';

// Thử mật khẩu rỗng (mặc định của XAMPP) trước, nếu không được thì thử 'root' (Docker/Linux)
$passwordsToTry = ['', 'root'];
$conn = null;
$lastException = null;

foreach ($passwordsToTry as $pass) {
    try {
        $conn = new PDO("mysql:host={$host};dbname={$dbname};charset=utf8mb4", $username, $pass, [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION, // Bật chế độ báo lỗi ngoại lệ
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,       // Mặc định trả về mảng kết hợp (Associative Array)
            PDO::ATTR_EMULATE_PREPARES   => false,                  // Sử dụng Native Prepared Statements
        ]);
        break; // Kết nối thành công, thoát khỏi vòng lặp
    } catch (PDOException $e) {
        $lastException = $e;
    }
}

// Xử lý trường hợp kết nối thất bại
if (!$conn) {
    die("<div style='color: #b91c1c; font-family: system-ui, -apple-system, sans-serif; padding: 24px; background: #fee2e2; border: 1px solid #f87171; border-radius: 8px; margin: 30px auto; max-width: 650px; line-height: 1.6;'>
            <h3 style='margin-top: 0; color: #991b1b;'>Lỗi kết nối Cơ sở dữ liệu!</h3>
            <p>Không thể kết nối tới MySQL Database <strong>{$dbname}</strong> tại máy chủ <strong>{$host}</strong>.</p>
            <p><strong>Nguyên nhân chi tiết:</strong> " . htmlspecialchars($lastException ? $lastException->getMessage() : 'Không rõ lỗi') . "</p>
            <hr style='border: none; border-top: 1px solid #fca5a5; margin: 15px 0;'>
            <p style='margin-bottom: 0; font-size: 0.9em; color: #7f1d1d;'>
                <em>Gợi ý: Hãy đảm bảo rằng MySQL đã được khởi động và bạn đã import file <code>shopping_cart.sql</code>.</em>
            </p>
         </div>");
}
