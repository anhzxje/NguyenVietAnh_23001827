<?php
/**
 * Model User: Quản lý người dùng và xác thực tài khoản (Authentication)
 * Bài tập tuần 5: Quản lý giỏ hàng (Shopping Cart)
 * Sinh viên: Nguyễn Việt Anh - MSV: 23001827
 */

require_once __DIR__ . '/../common/dbConnect.php';

/**
 * Tìm thông tin người dùng theo tên đăng nhập
 * @param string $username Tên đăng nhập
 * @return array|null Trả về thông tin user hoặc null nếu không tồn tại
 */
function getUserByUsername($username) {
    global $conn;
    try {
        $sql = "SELECT * FROM users WHERE username = :username LIMIT 1";
        $stmt = $conn->prepare($sql);
        $stmt->execute([':username' => $username]);
        $user = $stmt->fetch();
        return $user ?: null;
    } catch (PDOException $e) {
        error_log("Lỗi getUserByUsername: " . $e->getMessage());
        return null;
    }
}

/**
 * Tìm thông tin người dùng theo ID
 * @param int|string $id ID người dùng
 * @return array|null Trả về thông tin user hoặc null nếu không tồn tại
 */
function getUserById($id) {
    global $conn;
    try {
        $sql = "SELECT * FROM users WHERE id = :id LIMIT 1";
        $stmt = $conn->prepare($sql);
        $stmt->execute([':id' => $id]);
        $user = $stmt->fetch();
        return $user ?: null;
    } catch (PDOException $e) {
        error_log("Lỗi getUserById: " . $e->getMessage());
        return null;
    }
}

/**
 * Kiểm tra xem tên đăng nhập đã tồn tại trong CSDL hay chưa
 * @param string $username Tên đăng nhập cần kiểm tra
 * @return bool True nếu đã tồn tại, False nếu chưa
 */
function isUsernameExists($username) {
    return getUserByUsername($username) !== null;
}

/**
 * Đăng ký tài khoản người dùng mới
 * Mật khẩu bắt buộc phải được mã hóa bằng password_hash()
 * 
 * @param string $username Tên đăng nhập
 * @param string $password Mật khẩu dạng thuần
 * @return bool True nếu đăng ký thành công, False nếu thất bại
 */
function registerUser($username, $password) {
    global $conn;
    try {
        // Mã hóa mật khẩu an toàn theo yêu cầu đề bài
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

        $sql = "INSERT INTO users (username, password) VALUES (:username, :password)";
        $stmt = $conn->prepare($sql);
        return $stmt->execute([
            ':username' => $username,
            ':password' => $hashedPassword,
        ]);
    } catch (PDOException $e) {
        error_log("Lỗi registerUser: " . $e->getMessage());
        return false;
    }
}

/**
 * Xác thực thông tin đăng nhập của người dùng
 * Sử dụng password_verify() để kiểm tra mật khẩu đã mã hóa
 * 
 * @param string $username Tên đăng nhập
 * @param string $password Mật khẩu nhập vào
 * @return array|false Trả về mảng thông tin user nếu đúng, ngược lại trả về false
 */
function authenticateUser($username, $password) {
    $user = getUserByUsername($username);
    if (!$user) {
        return false; // Tên đăng nhập không tồn tại
    }

    // Kiểm tra mật khẩu bằng hàm chuẩn PHP password_verify()
    if (password_verify($password, $user['password'])) {
        return $user; // Mật khẩu chính xác
    }

    return false; // Mật khẩu sai
}
