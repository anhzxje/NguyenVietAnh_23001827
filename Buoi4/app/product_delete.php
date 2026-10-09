<?php
/**
 * Trang xóa sản phẩm
 * Bài tập tuần 4: Quản lý giỏ hàng
 * Sinh viên: Nguyễn Việt Anh - 23001827
 */

require_once __DIR__ . '/model/product.php';

$pageTitle = 'Xóa sản phẩm - Quản lý giỏ hàng';
$id        = $_GET['id'] ?? $_POST['id'] ?? null;
$confirm   = $_POST['confirm'] ?? $_GET['confirm'] ?? null;

// Kiểm tra ID hợp lệ
if (!$id || !is_numeric($id)) {
    require_once __DIR__ . '/view/header.php';
    ?>
    <div class="card" style="text-align: center; max-width: 600px; margin: 2rem auto; padding: 3rem 1.5rem;">
        <h2 style="color: var(--danger-color); margin-bottom: 0.75rem;">Yêu cầu không hợp lệ!</h2>
        <p style="color: var(--text-muted); margin-bottom: 1.5rem;">ID sản phẩm không được cung cấp hoặc sai định dạng.</p>
        <a href="product_list.php" class="btn btn-primary">Quay lại danh sách sản phẩm</a>
    </div>
    <?php
    require_once __DIR__ . '/view/footer.php';
    exit;
}

// Kiểm tra xem sản phẩm có tồn tại trong CSDL hay không
$product = getProductById((int)$id);

// Nếu sản phẩm không tồn tại, hiển thị thông báo phù hợp theo yêu cầu đề bài
if (!$product) {
    require_once __DIR__ . '/view/header.php';
    ?>
    <div class="card" style="text-align: center; max-width: 600px; margin: 2rem auto; padding: 3rem 1.5rem;">
        <h2 style="color: var(--danger-color); margin-bottom: 0.75rem;">Sản phẩm không tồn tại!</h2>
        <p style="color: var(--text-muted); margin-bottom: 1.5rem;">
            Sản phẩm với ID #<strong><?= htmlspecialchars((string)$id) ?></strong> không tồn tại trong hệ thống hoặc đã bị xóa trước đó.
        </p>
        <a href="product_list.php" class="btn btn-primary">Quay lại danh sách sản phẩm</a>
    </div>
    <?php
    require_once __DIR__ . '/view/footer.php';
    exit;
}

// Nếu đã xác nhận xóa (qua POST form xác nhận hoặc GET confirm=1)
if ($confirm === '1' || $confirm === 'yes') {
    $result = deleteProduct((int)$id);
    if ($result) {
        header('Location: product_list.php?status=deleted');
        exit;
    } else {
        header('Location: product_list.php?error=delete_failed');
        exit;
    }
}

// Nếu chưa xác nhận, hiển thị giao diện xác nhận xóa trước khi thực hiện
require_once __DIR__ . '/view/header.php';
?>

<div class="card confirm-card">
    <h2 style="color: var(--danger-color); margin-bottom: 0.5rem;">Xác nhận xóa sản phẩm</h2>
    <p style="color: var(--text-muted);">
        Bạn có chắc chắn muốn xóa sản phẩm này khỏi cơ sở dữ liệu không?<br>
        <strong>Hành động này không thể hoàn tác!</strong>
    </p>

    <!-- Thông tin chi tiết sản phẩm sẽ xóa -->
    <div class="confirm-details">
        <p><strong>ID:</strong> #<?= htmlspecialchars((string)$product['id']) ?></p>
        <p style="margin-top: 0.35rem;"><strong>Tên sản phẩm:</strong> <?= htmlspecialchars($product['name']) ?></p>
        <p style="margin-top: 0.35rem;"><strong>Giá:</strong> <?= number_format($product['price'], 0, ',', '.') ?> VNĐ</p>
        <p style="margin-top: 0.35rem;"><strong>Số lượng tồn:</strong> <?= htmlspecialchars((string)$product['quantity']) ?> cái</p>
    </div>

    <!-- Form gửi yêu cầu xác nhận xóa -->
    <form action="product_delete.php" method="POST" style="display: flex; gap: 1rem; justify-content: center;">
        <input type="hidden" name="id" value="<?= htmlspecialchars((string)$product['id']) ?>">
        <input type="hidden" name="confirm" value="yes">
        
        <button type="submit" class="btn btn-danger">
            Đồng ý Xóa
        </button>
        <a href="product_list.php" class="btn btn-secondary">
            Hủy bỏ
        </a>
    </form>
</div>

<?php
require_once __DIR__ . '/view/footer.php';
?>
