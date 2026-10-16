<?php
/**
 * Trang Đăng ký tài khoản người dùng mới
 * Bài tập tuần 5: Quản lý giỏ hàng & Phân quyền
 * Sinh viên: Nguyễn Việt Anh - MSV: 23001827
 */

require_once __DIR__ . '/common/auth.php';
require_once __DIR__ . '/model/user.php';

// Nếu đã đăng nhập thì chuyển đến trang danh sách sản phẩm
if (isLoggedIn()) {
    header('Location: product_list.php');
    exit;
}

$pageTitle = 'Đăng ký tài khoản - Soopi Kênh Người Bán';
$errors    = [];
$username  = '';

// Xử lý khi người dùng submit form đăng ký
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username        = trim($_POST['username'] ?? '');
    $password        = $_POST['password'] ?? '';
    $confirmPassword = $_POST['confirm_password'] ?? '';

    // 1. Kiểm tra các trường không được để trống
    if ($username === '') {
        $errors['username'] = 'Tên đăng nhập không được để trống.';
    } elseif (mb_strlen($username) < 3 || mb_strlen($username) > 50) {
        $errors['username'] = 'Tên đăng nhập phải từ 3 đến 50 ký tự.';
    } elseif (!preg_match('/^[a-zA-Z0-9_]+$/', $username)) {
        $errors['username'] = 'Tên đăng nhập chỉ chứa chữ cái, số và dấu gạch dưới (_).';
    } elseif (isUsernameExists($username)) {
        // 2. Kiểm tra tên đăng nhập chưa tồn tại trong database
        $errors['username'] = 'Tên đăng nhập này đã tồn tại trong hệ thống. Vui lòng chọn tên khác!';
    }

    // Kiểm tra mật khẩu
    if ($password === '') {
        $errors['password'] = 'Mật khẩu không được để trống.';
    } elseif (mb_strlen($password) < 6) {
        $errors['password'] = 'Mật khẩu phải có độ dài tối thiểu 6 ký tự.';
    }

    // Kiểm tra khớp mật khẩu xác nhận (nếu có gửi trường confirm_password trong form)
    if (isset($_POST['confirm_password'])) {
        if ($confirmPassword === '') {
            $errors['confirm_password'] = 'Vui lòng xác nhận lại mật khẩu.';
        } elseif ($password !== $confirmPassword) {
            $errors['confirm_password'] = 'Mật khẩu xác nhận không trùng khớp.';
        }
    }

    // 3. Nếu không có lỗi, tiến hành lưu vào CSDL (sử dụng password_hash)
    if (empty($errors)) {
        $result = registerUser($username, $password);

        if ($result) {
            // Lưu thông báo flash và chuyển đến trang đăng nhập (URL sạch)
            $_SESSION['flash_registered'] = true;
            header('Location: login.php');
            exit;
        } else {
            $errors['general'] = 'Đăng ký thất bại do lỗi hệ thống. Vui lòng thử lại!';
        }
    }
}

require_once __DIR__ . '/view/header.php';
?>

<div class="auth-wrapper">
    <div class="auth-card">
        <div class="auth-header">
            <h2>Đăng Ký Tài Khoản</h2>
            <p>Tạo tài khoản mới để truy cập hệ thống</p>
        </div>

        <!-- Thông báo lỗi tổng quát nếu có -->
        <?php if (!empty($errors['general'])): ?>
            <div class="alert alert-danger">
                <span><?= htmlspecialchars($errors['general']) ?></span>
            </div>
        <?php elseif (!empty($errors)): ?>
            <div class="alert alert-danger">
                <span>Vui lòng kiểm tra lại thông tin bị lỗi bên dưới!</span>
            </div>
        <?php endif; ?>

        <!-- Form Đăng ký tài khoản -->
        <form action="register.php" method="POST" novalidate>
            <!-- Tên đăng nhập -->
            <div class="form-group">
                <label for="username" class="form-label">
                    Tên đăng nhập <span class="required">*</span>
                </label>
                <input type="text" 
                       id="username" 
                       name="username" 
                       class="form-input <?= isset($errors['username']) ? 'is-invalid' : '' ?>" 
                       placeholder="Ví dụ: nguyenvanan" 
                       value="<?= htmlspecialchars($username) ?>" 
                       required 
                       autofocus>
                <?php if (isset($errors['username'])): ?>
                    <span class="error-text"><?= htmlspecialchars($errors['username']) ?></span>
                <?php else: ?>
                    <span class="form-hint">Từ 3 - 50 ký tự (chữ cái, chữ số, gạch dưới).</span>
                <?php endif; ?>
            </div>

            <!-- Mật khẩu -->
            <div class="form-group">
                <label for="password" class="form-label">
                    Mật khẩu <span class="required">*</span>
                </label>
                <input type="password" 
                       id="password" 
                       name="password" 
                       class="form-input <?= isset($errors['password']) ? 'is-invalid' : '' ?>" 
                       placeholder="Nhập mật khẩu (tối thiểu 6 ký tự)" 
                       required>
                <?php if (isset($errors['password'])): ?>
                    <span class="error-text"><?= htmlspecialchars($errors['password']) ?></span>
                <?php endif; ?>
            </div>

            <!-- Xác nhận mật khẩu -->
            <div class="form-group">
                <label for="confirm_password" class="form-label">
                    Xác nhận lại mật khẩu <span class="required">*</span>
                </label>
                <input type="password" 
                       id="confirm_password" 
                       name="confirm_password" 
                       class="form-input <?= isset($errors['confirm_password']) ? 'is-invalid' : '' ?>" 
                       placeholder="Nhập lại mật khẩu vừa đặt" 
                       required>
                <?php if (isset($errors['confirm_password'])): ?>
                    <span class="error-text"><?= htmlspecialchars($errors['confirm_password']) ?></span>
                <?php endif; ?>
            </div>

            <button type="submit" class="btn btn-primary auth-btn">
                Tạo Tài Khoản
            </button>
        </form>

        <div class="auth-footer">
            Đã có tài khoản? <a href="login.php">Đăng nhập ngay</a>
        </div>
    </div>
</div>

<?php
require_once __DIR__ . '/view/footer.php';
?>
