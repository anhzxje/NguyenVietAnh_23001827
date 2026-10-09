<?php
/**
 * Trang thêm sản phẩm mới
 * Bài tập tuần 4: Quản lý giỏ hàng
 * Sinh viên: Nguyễn Việt Anh - 23001827
 */

require_once __DIR__ . '/model/product.php';

$pageTitle = 'Thêm sản phẩm mới - Quản lý giỏ hàng';
$errors    = [];
$name      = '';
$price     = '';
$quantity  = '';

// Xử lý khi người dùng submit form
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name     = trim($_POST['name'] ?? '');
    $price    = trim($_POST['price'] ?? '');
    $quantity = trim($_POST['quantity'] ?? '');

    // 1. Kiểm tra Tên sản phẩm: Không được rỗng
    if ($name === '') {
        $errors['name'] = 'Tên sản phẩm không được để trống.';
    } elseif (mb_strlen($name) > 100) {
        $errors['name'] = 'Tên sản phẩm không được vượt quá 100 ký tự.';
    }

    // 2. Kiểm tra Giá: Giá > 0
    if ($price === '') {
        $errors['price'] = 'Vui lòng nhập giá sản phẩm.';
    } elseif (!is_numeric($price) || (float)$price <= 0) {
        $errors['price'] = 'Giá sản phẩm phải là số hợp lệ và lớn hơn 0.';
    }

    // 3. Kiểm tra Số lượng: Số lượng >= 0
    if ($quantity === '') {
        $errors['quantity'] = 'Vui lòng nhập số lượng sản phẩm.';
    } elseif (!is_numeric($quantity) || (int)$quantity < 0 || (int)$quantity != (float)$quantity) {
        $errors['quantity'] = 'Số lượng phải là số nguyên không âm (>= 0).';
    }

    // Nếu không có lỗi thì tiến hành thêm vào CSDL
    if (empty($errors)) {
        $result = addProduct($name, (float)$price, (int)$quantity);
        if ($result) {
            header('Location: product_list.php?status=added');
            exit;
        } else {
            $errors['general'] = 'Thêm sản phẩm thất bại do lỗi hệ thống. Vui lòng thử lại!';
        }
    }
}

require_once __DIR__ . '/view/header.php';
?>

<div class="card form-container">
    <div class="card-header-bar">
        <div>
            <h1 class="card-title">Thêm sản phẩm mới</h1>
            <p class="card-subtitle">Nhập đầy đủ thông tin để thêm sản phẩm vào kho</p>
        </div>
        <a href="product_list.php" class="btn btn-secondary">
            Quay lại
        </a>
    </div>

    <!-- Thông báo lỗi tổng quát nếu có -->
    <?php if (!empty($errors['general'])): ?>
        <div class="alert alert-danger">
            <span><?= htmlspecialchars($errors['general']) ?></span>
        </div>
    <?php elseif (!empty($errors)): ?>
        <div class="alert alert-danger">
            <span>Vui lòng kiểm tra lại các trường dữ liệu bị lỗi bên dưới!</span>
        </div>
    <?php endif; ?>

    <!-- Form nhập thông tin sản phẩm -->
    <form action="product_add.php" method="POST" novalidate>
        <!-- Tên sản phẩm -->
        <div class="form-group">
            <label for="name" class="form-label">
                Tên sản phẩm <span class="required">*</span>
            </label>
            <input type="text" 
                   id="name" 
                   name="name" 
                   class="form-input <?= isset($errors['name']) ? 'is-invalid' : '' ?>" 
                   placeholder="Ví dụ: iPhone 15 Pro Max 256GB" 
                   value="<?= htmlspecialchars($name) ?>" 
                   required autofocus>
            <?php if (isset($errors['name'])): ?>
                <span class="error-text"><?= htmlspecialchars($errors['name']) ?></span>
            <?php else: ?>
                <span class="form-hint">Tối đa 100 ký tự.</span>
            <?php endif; ?>
        </div>

        <!-- Giá sản phẩm -->
        <div class="form-group">
            <label for="price" class="form-label">
                Giá sản phẩm (VNĐ) <span class="required">*</span>
            </label>
            <input type="number" 
                   id="price" 
                   name="price" 
                   step="any"
                   min="0.01"
                   class="form-input <?= isset($errors['price']) ? 'is-invalid' : '' ?>" 
                   placeholder="Ví dụ: 25000000" 
                   value="<?= htmlspecialchars($price) ?>" 
                   required>
            <?php if (isset($errors['price'])): ?>
                <span class="error-text"><?= htmlspecialchars($errors['price']) ?></span>
            <?php else: ?>
                <span class="form-hint">Giá bán phải là số dương lớn hơn 0.</span>
            <?php endif; ?>
        </div>

        <!-- Số lượng sản phẩm -->
        <div class="form-group">
            <label for="quantity" class="form-label">
                Số lượng tồn kho <span class="required">*</span>
            </label>
            <input type="number" 
                   id="quantity" 
                   name="quantity" 
                   step="1"
                   min="0"
                   class="form-input <?= isset($errors['quantity']) ? 'is-invalid' : '' ?>" 
                   placeholder="Ví dụ: 10" 
                   value="<?= htmlspecialchars($quantity) ?>" 
                   required>
            <?php if (isset($errors['quantity'])): ?>
                <span class="error-text"><?= htmlspecialchars($errors['quantity']) ?></span>
            <?php else: ?>
                <span class="form-hint">Số lượng phải là số nguyên lớn hơn hoặc bằng 0.</span>
            <?php endif; ?>
        </div>

        <!-- Hành động -->
        <div class="form-actions">
            <button type="submit" class="btn btn-success">
                Thêm sản phẩm
            </button>
            <a href="product_list.php" class="btn btn-secondary">
                Hủy bỏ
            </a>
        </div>
    </form>
</div>

<?php
require_once __DIR__ . '/view/footer.php';
?>
