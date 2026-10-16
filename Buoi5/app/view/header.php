<?php
/**
 * Header template
 * Bài tập: Quản lý giỏ hàng
 * Sinh viên: Nguyễn Việt Anh - 23001827
 */

require_once __DIR__ . '/../common/auth.php';

$currentPage  = basename($_SERVER['PHP_SELF']);
$userLoggedIn = isLoggedIn();
$currentUser  = getCurrentUser();
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($pageTitle ?? 'Quản Lý Sản Phẩm - Soopi Kênh Người Bán') ?></title>
    <!-- Google Fonts: Roboto chuẩn tiếng Việt -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;700&display=swap" rel="stylesheet">
    <!-- Main Stylesheet -->
    <link rel="stylesheet" href="assets/style.css">
</head>
<body>

<!-- Top Utility Bar -->
<div class="top-bar">
    <div class="top-bar-container">
        <div class="top-bar-left">
            <span>Kênh Người Bán</span>
            <span class="top-bar-divider">|</span>
            <span>Hệ thống Quản lý Sản phẩm Soopi</span>
        </div>
        <div class="top-bar-right">
            <span>Sinh viên: <strong>Nguyễn Việt Anh</strong> (23001827)</span>
        </div>
    </div>
</div>

<!-- Navigation Bar -->
<nav class="navbar">
    <div class="nav-container">
        <a href="product_list.php" class="nav-brand">
            <span class="brand-name">Soopi</span>
            <span class="brand-sub">Kênh Người Bán</span>
        </a>
        <div style="display: flex; align-items: center;">
            <ul class="nav-links">
                <?php if ($userLoggedIn): ?>
                    <li>
                        <a href="product_list.php" class="nav-link <?= in_array($currentPage, ['index.php', 'product_list.php', 'product_edit.php', 'product_delete.php']) ? 'active' : '' ?>">
                            Danh sách sản phẩm
                        </a>
                    </li>
                    <li>
                        <a href="product_add.php" class="nav-link <?= $currentPage === 'product_add.php' ? 'active' : '' ?>">
                            Thêm sản phẩm
                        </a>
                    </li>
                <?php else: ?>
                    <li>
                        <a href="login.php" class="nav-link <?= $currentPage === 'login.php' ? 'active' : '' ?>">
                            Đăng nhập
                        </a>
                    </li>
                    <li>
                        <a href="register.php" class="nav-link <?= $currentPage === 'register.php' ? 'active' : '' ?>">
                            Đăng ký
                        </a>
                    </li>
                <?php endif; ?>
            </ul>

            <?php if ($userLoggedIn): ?>
                <div class="user-nav-box">
                    <span class="user-badge">
                        Xin chào, <strong><?= htmlspecialchars($currentUser['username']) ?></strong>
                    </span>
                    <a href="logout.php" class="btn-logout">
                        Đăng xuất
                    </a>
                </div>
            <?php endif; ?>
        </div>
    </div>
</nav>

<!-- Main Content Area -->
<main class="main-container">
