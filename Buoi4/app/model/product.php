<?php
/**
 * Model Product: Chứa các hàm truy vấn database cho bảng products
 * Bài tập tuần 4: Quản lý giỏ hàng
 * Sinh viên: Nguyễn Việt Anh - 23001827
 */

require_once __DIR__ . '/../common/dbConnect.php';

/**
 * Lấy danh sách tất cả sản phẩm
 * @return array Mảng danh sách sản phẩm (mới nhất lên đầu)
 */
function getAllProducts() {
    global $conn;
    try {
        $sql = "SELECT * FROM products ORDER BY id ASC";
        $stmt = $conn->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll();
    } catch (PDOException $e) {
        error_log("Lỗi getAllProducts: " . $e->getMessage());
        return [];
    }
}

/**
 * Lấy thông tin 1 sản phẩm theo ID
 * @param int|string $id ID sản phẩm cần tìm
 * @return array|null Trả về mảng thông tin sản phẩm hoặc null nếu không tồn tại
 */
function getProductById($id) {
    global $conn;
    try {
        $sql = "SELECT * FROM products WHERE id = :id LIMIT 1";
        $stmt = $conn->prepare($sql);
        $stmt->execute([':id' => $id]);
        $product = $stmt->fetch();
        return $product ?: null;
    } catch (PDOException $e) {
        error_log("Lỗi getProductById: " . $e->getMessage());
        return null;
    }
}

/**
 * Thêm sản phẩm mới vào CSDL
 * @param string $name Tên sản phẩm
 * @param float $price Giá sản phẩm
 * @param int $quantity Số lượng sản phẩm
 * @return bool True nếu thêm thành công, False nếu thất bại
 */
function addProduct($name, $price, $quantity) {
    global $conn;
    try {
        $sql = "INSERT INTO products (name, price, quantity) VALUES (:name, :price, :quantity)";
        $stmt = $conn->prepare($sql);
        return $stmt->execute([
            ':name'     => $name,
            ':price'    => $price,
            ':quantity' => $quantity,
        ]);
    } catch (PDOException $e) {
        error_log("Lỗi addProduct: " . $e->getMessage());
        return false;
    }
}

/**
 * Cập nhật thông tin sản phẩm theo ID
 * @param int|string $id ID sản phẩm cần cập nhật
 * @param string $name Tên sản phẩm mới
 * @param float $price Giá sản phẩm mới
 * @param int $quantity Số lượng mới
 * @return bool True nếu cập nhật thành công, False nếu thất bại
 */
function updateProduct($id, $name, $price, $quantity) {
    global $conn;
    try {
        $sql = "UPDATE products SET name = :name, price = :price, quantity = :quantity WHERE id = :id";
        $stmt = $conn->prepare($sql);
        return $stmt->execute([
            ':name'     => $name,
            ':price'    => $price,
            ':quantity' => $quantity,
            ':id'       => $id,
        ]);
    } catch (PDOException $e) {
        error_log("Lỗi updateProduct: " . $e->getMessage());
        return false;
    }
}

/**
 * Xóa sản phẩm khỏi CSDL theo ID
 * @param int|string $id ID sản phẩm cần xóa
 * @return bool True nếu xóa thành công, False nếu thất bại
 */
function deleteProduct($id) {
    global $conn;
    try {
        $sql = "DELETE FROM products WHERE id = :id";
        $stmt = $conn->prepare($sql);
        return $stmt->execute([':id' => $id]);
    } catch (PDOException $e) {
        error_log("Lỗi deleteProduct: " . $e->getMessage());
        return false;
    }
}

/**
 * Kiểm tra tính hợp lệ của dữ liệu sản phẩm (dùng chung cho thêm và sửa)
 * @param string $name Tên sản phẩm
 * @param string|float $price Giá sản phẩm
 * @param string|int $quantity Số lượng sản phẩm
 * @return array Mảng chứa các lỗi phát hiện (rỗng nếu hợp lệ)
 */
function validateProductData($name, $price, $quantity) {
    $errors = [];

    // 1. Kiểm tra Tên sản phẩm
    if ($name === '') {
        $errors['name'] = 'Tên sản phẩm không được để trống.';
    } elseif (mb_strlen($name) > 100) {
        $errors['name'] = 'Tên sản phẩm không được vượt quá 100 ký tự.';
    }

    // 2. Kiểm tra Giá sản phẩm: lớn hơn 0 và không vượt quá giới hạn DECIMAL(10,2) là 99.999.999,99
    if ($price === '') {
        $errors['price'] = 'Vui lòng nhập giá sản phẩm.';
    } elseif (!is_numeric($price) || (float)$price <= 0) {
        $errors['price'] = 'Giá sản phẩm phải là số hợp lệ và lớn hơn 0.';
    } elseif ((float)$price > 99999999.99) {
        $errors['price'] = 'Giá sản phẩm không được vượt quá 99.999.999,99 VNĐ.';
    }

    // 3. Kiểm tra Số lượng: số nguyên không âm và không vượt quá giới hạn INT là 2.147.483.647
    if ($quantity === '') {
        $errors['quantity'] = 'Vui lòng nhập số lượng sản phẩm.';
    } elseif (!is_numeric($quantity) || (int)$quantity < 0 || (int)$quantity != (float)$quantity) {
        $errors['quantity'] = 'Số lượng phải là số nguyên không âm (>= 0).';
    } elseif ((float)$quantity > 2147483647) {
        $errors['quantity'] = 'Số lượng sản phẩm không được vượt quá 2.147.483.647.';
    }

    return $errors;
}

