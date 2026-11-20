<?php
// Các tiện ích dùng chung trong bài 9, không phụ thuộc bài trước.
header('Content-Type: application/json; charset=UTF-8');
function sendError($message, $statusCode = 400) {
    http_response_code($statusCode);
    echo json_encode(['success' => false, 'message' => $message], JSON_UNESCAPED_UNICODE);
    exit;
}
// 3. Khởi tạo kết nối cơ sở dữ liệu MySQL bằng PDO
function getDatabaseConnection() {
    $host = getenv('DB_HOST') ?: '127.0.0.1';
    $port = getenv('DB_PORT') ?: '3306';
    $dbname = getenv('DB_NAME') ?: 'buoi9_product_management';
    $username = getenv('DB_USER') ?: 'root';
    $password = getenv('DB_PASS') !== false ? getenv('DB_PASS') : '';

    try {
        $dsn = "mysql:host={$host};port={$port};dbname={$dbname};charset=utf8mb4";
        $pdo = new PDO($dsn, $username, $password, [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false,
        ]);
    } catch (PDOException $e) {
        sendError('Không thể kết nối đến cơ sở dữ liệu MySQL. Hãy kiểm tra cấu hình kết nối.', 500);
    }

    return $pdo;
}

