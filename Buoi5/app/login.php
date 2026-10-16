<?php
/**
 * Trang Đăng nhập hệ thống
 * Bài tập tuần 5: Quản lý giỏ hàng & Phân quyền
 * Sinh viên: Nguyễn Việt Anh - MSV: 23001827
 */

require_once __DIR__ . '/common/auth.php';
require_once __DIR__ . '/model/user.php';

// Nếu người dùng đã đăng nhập rồi thì chuyển thẳng tới trang danh sách sản phẩm
if (isLoggedIn()) {
    header('Location: product_list.php');
    exit;
}

$pageTitle = 'Đăng nhập - Soopi Kênh Người Bán';
$error     = '';
$username  = '';

// Đọc thông báo trạng thái từ Flash session
$status     = $_SESSION['flash_status'] ?? '';
$errorMsg   = $_SESSION['flash_error'] ?? '';
$registered = !empty($_SESSION['flash_registered']);

// Xóa flash message ngay sau khi lấy ra để khi F5 / load lại trang thì thông báo biến mất hoàn toàn
unset($_SESSION['flash_status'], $_SESSION['flash_error'], $_SESSION['flash_registered']);

// Xử lý khi người dùng submit form đăng nhập
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';

    // 1. Kiểm tra không được để trống
    if ($username === '' || $password === '') {
        $error = 'Vui lòng nhập đầy đủ tên đăng nhập và mật khẩu!';
    } else {
        // 2. Xác thực với CSDL qua password_verify()
        $user = authenticateUser($username, $password);

        if ($user) {
            // Đăng nhập thành công: Lưu thông tin cần thiết vào $_SESSION
            $_SESSION['user_id']  = $user['id'];
            $_SESSION['username'] = $user['username'];

            // Chuyển hướng đến product_list.php theo yêu cầu đề bài
            header('Location: product_list.php');
            exit;
        } else {
            // Thông tin không chính xác: Hiển thị thông báo lỗi phù hợp
            $error = 'Tên đăng nhập hoặc mật khẩu không chính xác. Vui lòng thử lại!';
        }
    }
}

require_once __DIR__ . '/view/header.php';
?>

<div class="auth-wrapper">
    <div class="auth-card">
        <div class="auth-header">
            <h2>Đăng Nhập</h2>
            <p>Hệ thống Quản lý Giỏ hàng & Sản phẩm Soopi</p>
        </div>

        <!-- Thông báo từ flash session -->
        <?php if ($registered): ?>
            <div class="alert alert-success">
                <span>Đăng ký tài khoản thành công! Vui lòng đăng nhập bên dưới.</span>
            </div>
        <?php elseif ($status === 'logged_out'): ?>
            <div class="alert alert-info">
                <span>Bạn đã đăng xuất khỏi hệ thống thành công!</span>
            </div>
        <?php elseif ($errorMsg === 'unauthorized'): ?>
            <div class="alert alert-danger">
                <span>Vui lòng đăng nhập để truy cập chức năng này!</span>
            </div>
        <?php endif; ?>

        <!-- Thông báo lỗi khi đăng nhập sai -->
        <?php if (!empty($error)): ?>
            <div class="alert alert-danger">
                <span><?= htmlspecialchars($error) ?></span>
            </div>
        <?php endif; ?>

        <!-- Form Đăng nhập HTML cơ bản -->
        <form action="login.php" method="POST" novalidate>
            <div class="form-group">
                <label for="username" class="form-label">
                    Tên đăng nhập <span class="required">*</span>
                </label>
                <input type="text" 
                       id="username" 
                       name="username" 
                       class="form-input <?= !empty($error) ? 'is-invalid' : '' ?>" 
                       placeholder="Nhập tên đăng nhập" 
                       value="<?= htmlspecialchars($username) ?>" 
                       required 
                       autofocus>
            </div>

            <div class="form-group">
                <label for="password" class="form-label">
                    Mật khẩu <span class="required">*</span>
                </label>
                <input type="password" 
                       id="password" 
                       name="password" 
                       class="form-input <?= !empty($error) ? 'is-invalid' : '' ?>" 
                       placeholder="Nhập mật khẩu" 
                       required>
            </div>

            <button type="submit" class="btn btn-primary auth-btn">
                Đăng Nhập
            </button>
        </form>

        <!-- Tài khoản mẫu trong Database -->
        <div class="demo-box">
            <strong>Tài khoản mẫu:</strong> <code>admin</code> / Mật khẩu: <code>admin123</code>
        </div>

        <div class="auth-footer">
            Chưa có tài khoản? <a href="register.php">Đăng ký tài khoản mới</a>
        </div>
    </div>
</div>

<?php
require_once __DIR__ . '/view/footer.php';
?>
