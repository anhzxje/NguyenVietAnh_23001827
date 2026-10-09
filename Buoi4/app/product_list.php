<?php
/**
 * Trang danh sách sản phẩm
 * Bài tập tuần 4: Quản lý giỏ hàng
 * Sinh viên: Nguyễn Việt Anh - 23001827
 */

require_once __DIR__ . '/model/product.php';

$pageTitle = 'Danh sách sản phẩm - Quản lý giỏ hàng';
$products  = getAllProducts();

// Đọc thông báo trạng thái từ query parameter (nếu có)
$status = $_GET['status'] ?? '';
$error  = $_GET['error'] ?? '';

require_once __DIR__ . '/view/header.php';
?>

<div class="card">
    <div class="card-header-bar">
        <div>
            <h1 class="card-title">Danh sách sản phẩm</h1>
            <p class="card-subtitle">Quản lý kho hàng và thông tin sản phẩm trong hệ thống</p>
        </div>
        <a href="product_add.php" class="btn btn-primary">
            Thêm sản phẩm mới
        </a>
    </div>

    <!-- Thông báo kết quả thực hiện -->
    <?php if ($status === 'added'): ?>
        <div class="alert alert-success">
            <span>Thêm sản phẩm mới thành công!</span>
        </div>
    <?php elseif ($status === 'updated'): ?>
        <div class="alert alert-success">
            <span>Cập nhật thông tin sản phẩm thành công!</span>
        </div>
    <?php elseif ($status === 'deleted'): ?>
        <div class="alert alert-success">
            <span>Đã xóa sản phẩm khỏi hệ thống thành công!</span>
        </div>
    <?php endif; ?>

    <?php if ($error === 'not_found'): ?>
        <div class="alert alert-danger">
            <span>Sản phẩm không tồn tại hoặc đã bị xóa trước đó!</span>
        </div>
    <?php elseif ($error === 'delete_failed'): ?>
        <div class="alert alert-danger">
            <span>Xóa sản phẩm thất bại. Vui lòng thử lại!</span>
        </div>
    <?php endif; ?>

    <!-- Bảng danh sách sản phẩm -->
    <?php if (!empty($products)): ?>
        <div class="table-wrapper">
            <table class="table-custom">
                <thead>
                    <tr>
                        <th class="col-id">ID</th>
                        <th>Tên sản phẩm</th>
                        <th>Giá</th>
                        <th>Số lượng</th>
                        <th style="width: 170px; text-align: center;">Chức năng</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($products as $product): ?>
                        <tr>
                            <td class="col-id">#<?= htmlspecialchars($product['id']) ?></td>
                            <td class="col-name"><?= htmlspecialchars($product['name']) ?></td>
                            <td class="col-price">
                                <?= number_format($product['price'], 0, ',', '.') ?> đ
                            </td>
                            <td>
                                <strong><?= htmlspecialchars($product['quantity']) ?></strong>
                                <?php if ($product['quantity'] <= 0): ?>
                                    <span class="badge badge-out-of-stock">Hết hàng</span>
                                <?php elseif ($product['quantity'] <= 5): ?>
                                    <span class="badge badge-low-stock">Sắp hết</span>
                                <?php else: ?>
                                    <span class="badge badge-in-stock">Còn hàng</span>
                                <?php endif; ?>
                            </td>
                            <td style="text-align: center;">
                                <div class="action-buttons" style="justify-content: center;">
                                    <a href="product_edit.php?id=<?= urlencode($product['id']) ?>" 
                                       class="btn btn-sm btn-edit" 
                                       title="Chỉnh sửa sản phẩm">
                                        Sửa
                                    </a>
                                    <a href="product_delete.php?id=<?= urlencode($product['id']) ?>" 
                                       class="btn btn-sm btn-delete" 
                                       title="Xóa sản phẩm">
                                        Xóa
                                    </a>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <div style="margin-top: 1rem; color: var(--text-muted); font-size: 0.875rem;">
            Tổng cộng: <strong><?= count($products) ?></strong> sản phẩm trong danh sách.
        </div>
    <?php else: ?>
        <div style="text-align: center; padding: 3rem 1rem;">
            <h3 style="color: var(--text-dark); margin-bottom: 0.5rem;">Chưa có sản phẩm nào</h3>
            <p style="color: var(--text-muted); margin-bottom: 1.5rem;">Hệ thống chưa có sản phẩm nào trong kho. Hãy bắt đầu thêm ngay bây giờ.</p>
            <a href="product_add.php" class="btn btn-primary">Thêm sản phẩm đầu tiên</a>
        </div>
    <?php endif; ?>
</div>

<?php
require_once __DIR__ . '/view/footer.php';
?>
