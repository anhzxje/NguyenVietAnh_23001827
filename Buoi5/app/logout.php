<?php
/**
 * Chức năng Đăng xuất khỏi hệ thống
 * Bài tập tuần 5: Quản lý giỏ hàng & Phân quyền
 * Sinh viên: Nguyễn Việt Anh - MSV: 23001827
 */

require_once __DIR__ . '/common/auth.php';

// Xóa thông tin xác thực đăng nhập
unset($_SESSION['user_id'], $_SESSION['username']);

// Khởi tạo lại ID phiên để đảm bảo an toàn
session_regenerate_id(true);

// Lưu thông báo flash dạng phiên (sẽ tự hủy sau 1 lần hiển thị)
$_SESSION['flash_status'] = 'logged_out';

// Chuyển hướng sạch về trang login.php (không gắn query parameter)
header('Location: login.php');
exit;
