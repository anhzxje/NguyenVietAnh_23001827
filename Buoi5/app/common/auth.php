<?php
/**
 * Helper kiểm soát phiên làm việc và phân quyền truy cập (Session & Access Control)
 * Bài tập tuần 5: Quản lý giỏ hàng (Shopping Cart)
 * Sinh viên: Nguyễn Việt Anh - MSV: 23001827
 */

// Khởi tạo phiên làm việc nếu chưa có và header chưa được gửi
if (session_status() === PHP_SESSION_NONE && !headers_sent()) {
    session_start();
}

/**
 * Kiểm tra xem người dùng hiện tại đã đăng nhập hay chưa
 * @return bool True nếu đã đăng nhập, ngược lại False
 */
function isLoggedIn() {
    return !empty($_SESSION['user_id']) && !empty($_SESSION['username']);
}

/**
 * Bắt buộc người dùng phải đăng nhập mới được truy cập trang.
 * Nếu chưa đăng nhập, tự động chuyển hướng về login.php kèm thông báo.
 */
function requireLogin() {
    if (!isLoggedIn()) {
        $_SESSION['flash_error'] = 'unauthorized';
        header('Location: login.php');
        exit;
    }
}

/**
 * Lấy thông tin tài khoản người dùng hiện tại đang đăng nhập
 * @return array Mảng chứa 'id' và 'username'
 */
function getCurrentUser() {
    return [
        'id'       => $_SESSION['user_id'] ?? null,
        'username' => $_SESSION['username'] ?? '',
    ];
}
