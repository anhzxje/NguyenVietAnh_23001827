<?php
/**
 * Footer template
 * Bài tập: Quản lý giỏ hàng
 * Sinh viên: Nguyễn Việt Anh - 23001827
 */
?>
</main>

<footer class="footer">
    <div class="footer-container">
        <p class="footer-info">
            <strong>Soopi Kênh Người Bán &bull; Quản Lý Giỏ Hàng</strong> (Thực hành PHP + MySQL)
        </p>
        <p class="footer-author">
            Sinh viên thực hiện: <strong>Nguyễn Việt Anh</strong> &bull; Mã sinh viên: <strong>23001827</strong>
        </p>
        <p class="footer-copy">
            &copy; <?= date('Y') ?> Soopi. Hệ thống quản lý sản phẩm nội bộ phục vụ học tập.
        </p>
    </div>
</footer>

<script>
    // Tự động làm sạch query string trên URL trình duyệt để tránh lưu trạng thái thông báo cũ khi F5
    if (window.history && window.history.replaceState && window.location.search) {
        window.history.replaceState(null, document.title, window.location.pathname);
    }
</script>

</body>
</html>
