## Hướng dẫn cài đặt & Chạy ứng dụng

### Bước 1: Khởi động Apache & MySQL trong XAMPP
1. Mở **XAMPP Control Panel**.
2. Nhấn nút **Start** ở cả 2 module: **Apache** và **MySQL**.

### Bước 2: Import Cơ sở dữ liệu (Database)
1. Mở trình duyệt và truy cập: `http://localhost/phpmyadmin/`
2. Chọn tab **Import** (Nhập).
3. Chọn file `shopping_cart.sql` trong thư mục `Buoi5/` rồi nhấn **Import**.
*(Cơ sở dữ liệu `shopping_cart` cùng 2 bảng `products` và `users` sẽ được tạo kèm dữ liệu mẫu).*

### Bước 3: Truy cập ứng dụng qua trình duyệt
Mở trình duyệt và truy cập theo đường dẫn:
```text
http://localhost/NguyenVietAnh_23001827/Buoi5/app/
```