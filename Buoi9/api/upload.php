<?php
require_once __DIR__ . '/common.php';

if (($_SERVER['REQUEST_METHOD'] ?? '') !== 'POST') {
    header('Allow: POST');
    sendError('Chỉ chấp nhận phương thức POST.', 405);
}

// Nếu post_max_size bị vượt, PHP bỏ trống cả $_POST lẫn $_FILES.
if (empty($_POST) && empty($_FILES) && (int)($_SERVER['CONTENT_LENGTH'] ?? 0) > 0) {
    sendError('Không nhận được file. Ảnh tối đa 2 MB; kiểm tra giới hạn upload của PHP.');
}
$rawId = $_POST['product_id'] ?? null;
$id = is_string($rawId) ? filter_var($rawId, FILTER_VALIDATE_INT) : false;
if ($id === false || $id <= 0) {
    sendError('Mã sản phẩm không hợp lệ.');
}
$file = $_FILES['image'] ?? null;
if (!is_array($file) || !isset($file['error']) || is_array($file['error'])) {
    sendError('Vui lòng chọn một ảnh JPG, JPEG hoặc PNG.');
}
if ($file['error'] !== UPLOAD_ERR_OK) {
    sendError(in_array($file['error'], [UPLOAD_ERR_INI_SIZE, UPLOAD_ERR_FORM_SIZE], true)
        ? 'Ảnh vượt quá dung lượng cho phép (tối đa 2 MB).'
        : 'Không nhận được ảnh hoàn chỉnh. Vui lòng chọn ảnh và thử lại.');
}
$tmp = $file['tmp_name'];
$size = is_uploaded_file($tmp) ? filesize($tmp) : false;
if ($size === false || $size === 0 || $size > 2 * 1024 * 1024) {
    sendError('Ảnh phải có dung lượng lớn hơn 0 và tối đa 2 MB.');
}
$extension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
$mime = (new finfo(FILEINFO_MIME_TYPE))->file($tmp);
$info = @getimagesize($tmp);
$allowed = ['jpg' => 'image/jpeg', 'jpeg' => 'image/jpeg', 'png' => 'image/png'];
if (!isset($allowed[$extension]) || $allowed[$extension] !== $mime || !$info || $info['mime'] !== $mime) {
    sendError('File phải là ảnh JPG, JPEG hoặc PNG thực sự.');
}

$pdo = getDatabaseConnection();
$destination = null;
try {
    // Khóa dòng trong lúc thay ảnh, tránh hai lần upload cập nhật chồng nhau.
    $pdo->beginTransaction();
    $stmt = $pdo->prepare('SELECT id, image FROM products WHERE id = :id FOR UPDATE');
    $stmt->execute([':id' => $id]);
    $product = $stmt->fetch();
    if (!$product) {
        $pdo->rollBack();
        sendError('Sản phẩm không tồn tại.', 404);
    }
    $directory = dirname(__DIR__) . '/uploads';
    if (!is_dir($directory) && !@mkdir($directory, 0755, true) && !is_dir($directory)) {
        throw new RuntimeException('Không tạo được thư mục uploads.');
    }
    $path = 'uploads/' . bin2hex(random_bytes(16)) . ($mime === 'image/png' ? '.png' : '.jpg');
    $destination = dirname(__DIR__) . '/' . $path;
    if (!@move_uploaded_file($tmp, $destination)) {
        throw new RuntimeException('Không lưu được ảnh.');
    }
    $stmt = $pdo->prepare('UPDATE products SET image = :image WHERE id = :id');
    $stmt->execute([':image' => $path, ':id' => $id]);
    $pdo->commit();
} catch (Throwable $e) {
    if ($pdo->inTransaction()) $pdo->rollBack();
    if ($destination && is_file($destination)) @unlink($destination);
    sendError('Upload thất bại. Kiểm tra kết nối và quyền ghi thư mục uploads rồi thử lại. Ảnh cũ được giữ nguyên.', 500);
}
// Chỉ dọn ảnh cũ sau khi file mới và đường dẫn mới đã lưu thành công.
if ($product['image'] && preg_match('~^uploads/[a-f0-9]+\.(png|jpg)$~D', $product['image'])) {
    @unlink(dirname(__DIR__) . '/' . $product['image']);
}
echo json_encode([
    'success' => true,
    'message' => 'Upload ảnh thành công.',
    'data' => ['id' => (int)$id, 'image' => $path]
], JSON_UNESCAPED_UNICODE);
