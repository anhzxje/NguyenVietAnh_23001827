<?php
/**
 * Điểm vào chính của ứng dụng
 * Tự động chuyển hướng tùy theo trạng thái đăng nhập của người dùng
 * Bài tập tuần 5: Quản lý giỏ hàng & Phân quyền
 * Sinh viên: Nguyễn Việt Anh - MSV: 23001827
 */

require_once __DIR__ . '/common/auth.php';

if (isLoggedIn()) {
    header('Location: product_list.php');
} else {
    header('Location: login.php');
}
exit;
