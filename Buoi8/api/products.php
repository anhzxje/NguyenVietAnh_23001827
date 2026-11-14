<?php
/**
 * ============================================================
 * CÂU 2: XÂY DỰNG RESTFUL API BẰNG PHP
 * Sinh viên: Nguyễn Việt Anh - MSV: 23001827
 * Đường dẫn: api/products.php
 * ============================================================
 */

// 1. Cấu hình Header phản hồi chuẩn JSON & CORS
header('Content-Type: application/json; charset=UTF-8');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With');

// Phản hồi cho preflight request (OPTIONS) từ trình duyệt
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    echo json_encode(['success' => true]);
    exit;
}

// 2. Hàm hỗ trợ trả về JSON và kết thúc
function sendResponse($success, $dataOrMessage, $statusCode = 200) {
    http_response_code($statusCode);
    $response = ['success' => (bool)$success];
    if ($success) {
        if (is_array($dataOrMessage) && array_key_exists('data', $dataOrMessage)) {
            $response = array_merge($response, $dataOrMessage);
        } else if (is_array($dataOrMessage)) {
            $response['data'] = $dataOrMessage;
        } else {
            $response['message'] = (string)$dataOrMessage;
        }
    } else {
        $response['message'] = (string)$dataOrMessage;
    }
    echo json_encode($response, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
    exit;
}

function sendError($message, $statusCode = 400) {
    sendResponse(false, $message, $statusCode);
}

// 3. Khởi tạo kết nối cơ sở dữ liệu MySQL bằng PDO
function getDatabaseConnection() {
    $host = getenv('DB_HOST') ?: '127.0.0.1';
    $port = getenv('DB_PORT') ?: '3306';
    $dbname = getenv('DB_NAME') ?: 'product_management';
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

// 4. Kiểm tra tính hợp lệ của dữ liệu (Validation)
function validateProductData($data) {
    if (!is_array($data)) {
        sendError('Dữ liệu gửi lên không đúng định dạng JSON.');
    }

    // Kiểm tra tên sản phẩm: sau khi trim phải có 1 - 100 ký tự
    if (!isset($data['name']) || !is_string($data['name'])) {
        sendError('Vui lòng nhập tên sản phẩm.');
    }
    $name = trim($data['name']);
    $nameLength = preg_match_all('/./us', $name);
    if ($nameLength === false || $nameLength < 1 || $nameLength > 100) {
        sendError('Tên sản phẩm sau khi bỏ khoảng trắng hai đầu phải có từ 1 đến 100 ký tự.');
    }

    // Kiểm tra đơn giá: số nguyên lớn hơn 0
    if (!isset($data['price']) || $data['price'] === '') {
        sendError('Vui lòng nhập đơn giá.');
    }
    $rawPrice = $data['price'];
    if (!is_numeric($rawPrice) || filter_var($rawPrice, FILTER_VALIDATE_INT) === false) {
        sendError('Đơn giá phải là số nguyên.');
    }
    $price = (int)$rawPrice;
    if ($price <= 0 || $price > 2147483647) {
        sendError('Đơn giá phải là số nguyên từ 1 đến 2147483647.');
    }

    // Kiểm tra số lượng tồn: số nguyên không âm (>= 0)
    if (!isset($data['stock']) || $data['stock'] === '') {
        sendError('Vui lòng nhập số lượng tồn.');
    }
    $rawStock = $data['stock'];
    if (!is_numeric($rawStock) || filter_var($rawStock, FILTER_VALIDATE_INT) === false) {
        sendError('Số lượng tồn phải là số nguyên.');
    }
    $stock = (int)$rawStock;
    if ($stock < 0 || $stock > 2147483647) {
        sendError('Số lượng tồn phải là số nguyên từ 0 đến 2147483647.');
    }

    return [
        'name'  => $name,
        'price' => $price,
        'stock' => $stock
    ];
}

// 5. Điều hướng xử lý theo phương thức HTTP
$method = $_SERVER['REQUEST_METHOD'] ?? 'GET';
$pdo = getDatabaseConnection();

try {
    switch ($method) {
        // ----------------------------------------------------
        // GET: Trả về danh sách sản phẩm
        // ----------------------------------------------------
        case 'GET':
            $stmt = $pdo->prepare("SELECT id, name, price, stock FROM products ORDER BY id ASC");
            $stmt->execute();
            $products = $stmt->fetchAll();

            foreach ($products as &$p) {
                $p['id'] = (int)$p['id'];
                $p['price'] = (int)$p['price'];
                $p['stock'] = (int)$p['stock'];
            }

            echo json_encode([
                'success' => true,
                'data'    => $products
            ], JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
            break;

        // ----------------------------------------------------
        // POST: Thêm sản phẩm mới
        // ----------------------------------------------------
        case 'POST':
            $rawInput = file_get_contents('php://input');
            $data = json_decode($rawInput, true);

            $validated = validateProductData($data);

            $stmt = $pdo->prepare("INSERT INTO products (name, price, stock) VALUES (:name, :price, :stock)");
            $stmt->execute([
                ':name'  => $validated['name'],
                ':price' => $validated['price'],
                ':stock' => $validated['stock']
            ]);

            $newId = (int)$pdo->lastInsertId();

            echo json_encode([
                'success' => true,
                'message' => 'Thêm sản phẩm thành công.',
                'data'    => [
                    'id'    => $newId,
                    'name'  => $validated['name'],
                    'price' => $validated['price'],
                    'stock' => $validated['stock']
                ]
            ], JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
            break;

        // ----------------------------------------------------
        // PUT: Cập nhật sản phẩm theo id trên URL
        // ----------------------------------------------------
        case 'PUT':
            $rawId = $_GET['id'] ?? null;
            $id = filter_var($rawId, FILTER_VALIDATE_INT);
            if ($id === false || $id <= 0) {
                sendError('Mã sản phẩm (id) không hợp lệ hoặc thiếu trên URL.', 400);
            }

            // Kiểm tra xem sản phẩm có tồn tại hay không
            $stmtCheck = $pdo->prepare("SELECT id FROM products WHERE id = :id");
            $stmtCheck->execute([':id' => $id]);
            if (!$stmtCheck->fetch()) {
                sendError("Sản phẩm có mã id = {$id} không tồn tại.", 404);
            }

            $rawInput = file_get_contents('php://input');
            $data = json_decode($rawInput, true);

            $validated = validateProductData($data);

            $stmtUpdate = $pdo->prepare("UPDATE products SET name = :name, price = :price, stock = :stock WHERE id = :id");
            $stmtUpdate->execute([
                ':name'  => $validated['name'],
                ':price' => $validated['price'],
                ':stock' => $validated['stock'],
                ':id'    => $id
            ]);

            echo json_encode([
                'success' => true,
                'message' => 'Cập nhật sản phẩm thành công.',
                'data'    => [
                    'id'    => $id,
                    'name'  => $validated['name'],
                    'price' => $validated['price'],
                    'stock' => $validated['stock']
                ]
            ], JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
            break;

        // ----------------------------------------------------
        // DELETE: Xóa sản phẩm theo id trên URL
        // ----------------------------------------------------
        case 'DELETE':
            $rawId = $_GET['id'] ?? null;
            $id = filter_var($rawId, FILTER_VALIDATE_INT);
            if ($id === false || $id <= 0) {
                sendError('Mã sản phẩm (id) không hợp lệ hoặc thiếu trên URL.', 400);
            }

            // Kiểm tra xem sản phẩm có tồn tại hay không
            $stmtCheck = $pdo->prepare("SELECT id FROM products WHERE id = :id");
            $stmtCheck->execute([':id' => $id]);
            if (!$stmtCheck->fetch()) {
                sendError("Sản phẩm có mã id = {$id} không tồn tại.", 404);
            }

            $stmtDelete = $pdo->prepare("DELETE FROM products WHERE id = :id");
            $stmtDelete->execute([':id' => $id]);

            echo json_encode([
                'success' => true,
                'message' => "Đã xóa thành công sản phẩm có mã id = {$id}."
            ], JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
            break;

        default:
            sendError('Phương thức HTTP không được hỗ trợ.', 405);
            break;
    }
} catch (PDOException $e) {
    sendError('Lỗi cơ sở dữ liệu.', 500);
} catch (Exception $e) {
    sendError('Lỗi máy chủ.', 500);
}
