# BÀI THỰC HÀNH BUỔI 8: PHP (RESTful API) VÀ VUE.JS

* **Môn học:** Lập trình Web
* **Sinh viên thực hiện:** Nguyễn Việt Anh
* **Mã sinh viên:** 23001827

---

## 1. Cấu trúc thư mục bài tập (`Buoi8/`)

```text
Buoi8/
├── api/
│   └── products.php       # Backend RESTful API bằng PHP (GET, POST, PUT, DELETE)
├── database.sql           # Cú pháp tạo bảng products và 3 dữ liệu mẫu (Câu 1)
├── index.html             # Giao diện người dùng hoàn chỉnh bằng Vue.js 3 (Câu 3)
└── README.md              # Tài liệu chi tiết hướng dẫn cài đặt và kiểm thử (Câu 4)
```

---

## 2. Nội dung bài tập

### Câu 1: Cơ sở dữ liệu (`database.sql`)

#### Cấu trúc bảng `products`:
* `id INT`: Khóa chính, tự động tăng (`AUTO_INCREMENT PRIMARY KEY`).
* `name VARCHAR(100) NOT NULL`: Tên sản phẩm, bắt buộc nhập, tối đa 100 ký tự.
* `price INT NOT NULL`: Đơn giá (đồng), số nguyên lớn hơn 0 (`CHECK (price > 0)`).
* `stock INT NOT NULL`: Số lượng tồn, số nguyên không âm (`CHECK (stock >= 0)`).

#### Dữ liệu mẫu khởi tạo:
| Tên sản phẩm | Đơn giá (đồng) | Số lượng tồn |
| :--- | :--- | :--- |
| Bút bi | 5.000 | 20 |
| Vở ô ly | 12.000 | 15 |
| Thước kẻ | 8.000 | 0 |

---

### Câu 2: Đặc tả RESTful API (`api/products.php`)

Mọi phản hồi từ API đều ở dạng JSON, có trường `success` (boolean) cho biết kết quả thao tác.

#### Lấy danh sách sản phẩm
* **Phương thức:** `GET`
* **Đường dẫn:** `api/products.php`
* **Phản hồi thành công (200 OK):**
```json
{
  "success": true,
  "data": [
    {
      "id": 1,
      "name": "Bút bi",
      "price": 5000,
      "stock": 20
    },
    {
      "id": 2,
      "name": "Vở ô ly",
      "price": 12000,
      "stock": 15
    },
    {
      "id": 3,
      "name": "Thước kẻ",
      "price": 8000,
      "stock": 0
    }
  ]
}
```

#### Thêm sản phẩm mới
* **Phương thức:** `POST`
* **Đường dẫn:** `api/products.php`
* **Dữ liệu gửi (JSON Body):**
```json
{
  "name": "Hộp bút",
  "price": 25000,
  "stock": 10
}
```
* **Phản hồi thành công (200 / 201):**
```json
{
  "success": true,
  "message": "Thêm sản phẩm thành công.",
  "data": {
    "id": 4,
    "name": "Hộp bút",
    "price": 25000,
    "stock": 10
  }
}
```
* **Phản hồi thất bại (400 Bad Request):**
```json
{
  "success": false,
  "message": "Số lượng tồn phải là số nguyên từ 0 đến 2147483647."
}
```

#### Cập nhật sản phẩm
* **Phương thức:** `PUT`
* **Đường dẫn:** `api/products.php?id=1`
* **Dữ liệu gửi (JSON Body):**
```json
{
  "name": "Bút bi Thiên Long",
  "price": 6000,
  "stock": 18
}
```
* **Phản hồi thành công (200 OK):**
```json
{
  "success": true,
  "message": "Cập nhật sản phẩm thành công.",
  "data": {
    "id": 1,
    "name": "Bút bi Thiên Long",
    "price": 6000,
    "stock": 18
  }
}
```
* **Phản hồi khi sản phẩm không tồn tại (404 Not Found):**
```json
{
  "success": false,
  "message": "Sản phẩm có mã id = 999 không tồn tại."
}
```

#### Xóa sản phẩm
* **Phương thức:** `DELETE`
* **Đường dẫn:** `api/products.php?id=1`
* **Phản hồi thành công (200 OK):**
```json
{
  "success": true,
  "message": "Đã xóa thành công sản phẩm có mã id = 1."
}
```
* **Phản hồi khi sản phẩm không tồn tại (404 Not Found):**
```json
{
  "success": false,
  "message": "Sản phẩm có mã id = 999 không tồn tại."
}
```

---

### Câu 3: Giao diện người dùng bằng Vue.js (`index.html`)

1. **Khởi tạo và hiển thị danh sách:** Khi mở trang, gọi tự động API `GET api/products.php` và render bảng bằng directive `v-for`. Mỗi dòng có đầy đủ Tên, Đơn giá (định dạng VND), Số lượng tồn (badge trạng thái Còn hàng / Hết hàng) cùng 2 nút hành động `Sửa` và `Xóa`.
2. **Biểu mẫu Thêm sản phẩm:** Gồm 3 trường liên kết hai chiều qua `v-model`. Bấm nút **Lưu** sẽ gửi yêu cầu `POST`.
3. **Chế độ Sửa sản phẩm:** Bấm **Sửa** tại một dòng sẽ đưa dữ liệu sản phẩm đó lên form, chuyển tiêu đề sang "Cập Nhật Sản Phẩm" và bật nút **Hủy bỏ**. Khi bấm **Lưu**, gửi yêu cầu `PUT` kèm `id` trên URL.
4. **Xác nhận khi Xóa:** Bấm **Xóa** sẽ hiển thị hộp thoại xác nhận `window.confirm(...)`. Chỉ khi người dùng chọn Đồng ý thì mới gửi request `DELETE`.
5. **Cập nhật không tải lại trang:** Sau khi Thêm, Sửa hoặc Xóa thành công, tự động gọi lại hàm nạp dữ liệu để cập nhật bảng mượt mà. Mọi phản hồi thành công hoặc lỗi từ backend đều được hiển thị thông báo trực quan trên giao diện.

---

### Câu 4: Kiểm tra các tình huống thực nghiệm

Các tình huống dưới đây đã được người thực hiện kiểm tra và đều thành công.

| STT | Thao tác kiểm tra | Cách thực hiện | Kết quả kiểm tra |
| :---: | :--- | :--- | :--- |
| **1** | **Thêm sản phẩm hợp lệ rồi tải lại trang** | Nhập `Tên: Tẩy gôm`, `Đơn giá: 4000`, `Số lượng tồn: 50` -> Bấm **Lưu** -> F5 (Tải lại trang) | **Thành công:** Sản phẩm vẫn hiển thị sau khi tải lại trang. |
| **2** | **Sửa đơn giá của một sản phẩm** | Bấm nút **Sửa** ở sản phẩm "Bút bi", sửa đơn giá từ `5000` thành `7000` -> Bấm **Lưu** | **Thành công:** Bảng hiển thị đơn giá mới `7.000 đ` mà không tải lại trang. |
| **3** | **Gửi trực tiếp yêu cầu thêm sản phẩm với tồn kho bằng -1** | Gửi yêu cầu POST qua API với body: `{"name": "Thước cuộn", "price": 15000, "stock": -1}` | **Thành công:** PHP trả lỗi HTTP 400 và MySQL không có bản ghi mới. |
| **4** | **Xóa một sản phẩm rồi tải lại trang** | Bấm nút **Xóa** ở một sản phẩm -> Bấm Xác nhận (OK) -> Sau đó F5 tải lại trang | **Thành công:** Sản phẩm không còn xuất hiện sau khi tải lại trang. |

---

## 3. Hướng dẫn cài đặt & chạy ứng dụng

### Cách 1: Chạy qua XAMPP (Apache + MySQL)
1. Mở ứng dụng **XAMPP Control Panel**.
2. Nhấn nút **Start** ở cả 2 module: **Apache** và **MySQL**.
3. Mở trình duyệt và truy cập: `http://localhost/phpmyadmin/`
4. Chọn tab **Import** (Nhập) -> Chọn file `database.sql` trong thư mục `Buoi8/` rồi nhấn **Import**.
5. Mở trình duyệt và truy cập theo đường dẫn:
```text
http://localhost/NguyenVietAnh_23001827/Buoi8/
```

### Cách 2: Chạy nhanh bằng PHP Built-in Server
Cần cài PHP với extension `pdo_mysql` và chạy MySQL. Import `database.sql` trước khi chạy (lưu ý file này xóa bảng `products` cũ). Mặc định API kết nối `127.0.0.1:3306`, cơ sở dữ liệu `product_management`, người dùng `root`, mật khẩu rỗng. Có thể đặt các biến môi trường `DB_HOST`, `DB_PORT`, `DB_NAME`, `DB_USER`, `DB_PASS` nếu cấu hình khác.

Mở terminal tại thư mục `Buoi8`:
```bash
cd Buoi8
php -S localhost:8000
```
Sau đó truy cập trình duyệt tại: `http://localhost:8000`

---
