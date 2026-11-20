<?php
require_once __DIR__ . "/common.php";

// Kiểm tra thông tin sản phẩm gửi bằng JSON
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
            $rawPage = $_GET['page'] ?? '1';
            $rawLimit = $_GET['limit'] ?? '5';
            if (!is_string($rawPage) || !preg_match('/^[1-9][0-9]*$/D', $rawPage)
                || filter_var($rawPage, FILTER_VALIDATE_INT) === false) {
                sendError('page phải là số nguyên dương.');
            }
            if (!is_string($rawLimit) || $rawLimit !== '5') {
                sendError('limit phải bằng 5.');
            }
            $limit = 5;
            $total = (int)$pdo->query('SELECT COUNT(*) FROM products')->fetchColumn();
            $totalPages = max(1, (int)ceil($total / $limit));
            $page = min((int)$rawPage, $totalPages);
            $stmt = $pdo->prepare("SELECT id, name, price, stock, image FROM products ORDER BY id DESC LIMIT :limit OFFSET :offset");
            $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
            $stmt->bindValue(':offset', ($page - 1) * $limit, PDO::PARAM_INT);
            $stmt->execute();
            $products = $stmt->fetchAll();

            foreach ($products as &$p) {
                $p['id'] = (int)$p['id'];
                $p['price'] = (int)$p['price'];
                $p['stock'] = (int)$p['stock'];
            }

            echo json_encode([
                'success' => true,
                'data'    => $products,
                'pagination' => ['page' => $page, 'limit' => $limit, 'total' => $total, 'total_pages' => $totalPages]
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
                    'stock' => $validated['stock'],
                    'image' => null
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
            $stmtCheck = $pdo->prepare("SELECT id, image FROM products WHERE id = :id");
            $stmtCheck->execute([':id' => $id]);
            if (!($existing = $stmtCheck->fetch())) {
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
                    'stock' => $validated['stock'],
                    'image' => $existing['image']
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
            $stmtCheck = $pdo->prepare("SELECT id, image FROM products WHERE id = :id");
            $stmtCheck->execute([':id' => $id]);
            if (!($existing = $stmtCheck->fetch())) {
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
