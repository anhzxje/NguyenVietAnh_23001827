<?php
/**
 * Trang chỉnh sửa sản phẩm
 * Bài tập tuần 4: Quản lý giỏ hàng
 * Sinh viên: Nguyễn Việt Anh - 23001827
 */

require_once __DIR__ . '/model/product.php';

$pageTitle = 'Chỉnh sửa sản phẩm - Quản lý giỏ hàng';
$errors    = [];
$id        = $_GET['id'] ?? $_POST['id'] ?? null;

// Kiểm tra ID hợp lệ
if (!$id || !is_numeric($id)) {
    header('Location: product_list.php?error=not_found');
    exit;
}

// Lấy thông tin sản phẩm hiện tại
$product = getProductById((int)$id);

// Nếu không tìm thấy sản phẩm trong CSDL
if (!$product) {
    require_once __DIR__ . '/view/header.php';
    echo '<div class="card" style="text-align: center; max-width: 600px; margin: 2rem auto; padding: 3rem 1.5rem;">
            <h2 style="color: var(--danger-color); margin-bottom: 0.75rem;">Không tìm thấy sản phẩm!</h2>
            <p style="color: var(--text-muted); margin-bottom: 1.5rem;">Sản phẩm với ID #' . htmlspecialchars((string)$id) . ' không tồn tại hoặc đã bị xóa khỏi hệ thống.</p>
            <a href="product_list.php" class="btn btn-primary">Quay lại danh sách sản phẩm</a>
          </div>';
    require_once __DIR__ . '/view/footer.php';
    exit;
}

$name     = $product['name'];
$price    = $product['price'];
$quantity = $product['quantity'];

// Xử lý khi người dùng submit cập nhật sản phẩm
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

    // Nếu dữ liệu hợp lệ thì thực hiện cập nhật
    if (empty($errors)) {
        $result = updateProduct((int)$id, $name, (float)$price, (int)$quantity);
        if ($result) {
            header('Location: product_list.php?status=updated');
            exit;
        } else {
            $errors['general'] = 'Cập nhật thất bại do lỗi hệ thống hoặc không có thay đổi nào. Vui lòng thử lại!';
        }
    }
}

require_once __DIR__ . '/view/header.php';
?>

<div class="card form-container">
    <div class="card-header-bar">
        <div>
            <h1 class="card-title">Chỉnh sửa sản phẩm</h1>
            <p class="card-subtitle">Cập nhật thông tin chi tiết cho sản phẩm #<?= htmlspecialchars((string)$id) ?></p>
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

    <!-- Form sửa thông tin sản phẩm -->
    <form action="product_edit.php?id=<?= urlencode((string)$id) ?>" method="POST" novalidate>
        <!-- Ẩn ID sản phẩm -->
        <input type="hidden" name="id" value="<?= htmlspecialchars((string)$id) ?>">

        <!-- Tên sản phẩm -->
        <div class="form-group">
            <label for="name" class="form-label">
                Tên sản phẩm <span class="required">*</span>
            </label>
            <input type="text" 
                   id="name" 
                   name="name" 
                   class="form-input <?= isset($errors['name']) ? 'is-invalid' : '' ?>" 
                   placeholder="Nhập tên sản phẩm" 
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
                   placeholder="Nhập giá sản phẩm" 
                   value="<?= htmlspecialchars((string)$price) ?>" 
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
                   placeholder="Nhập số lượng sản phẩm" 
                   value="<?= htmlspecialchars((string)$quantity) ?>" 
                   required>
            <?php if (isset($errors['quantity'])): ?>
                <span class="error-text"><?= htmlspecialchars($errors['quantity']) ?></span>
            <?php else: ?>
                <span class="form-hint">Số lượng phải là số nguyên lớn hơn hoặc bằng 0.</span>
            <?php endif; ?>
        </div>

        <!-- Hành động -->
        <div class="form-actions">
            <button type="submit" class="btn btn-primary">
                Cập nhật sản phẩm
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
