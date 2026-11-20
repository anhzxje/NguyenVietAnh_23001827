# BÀI THỰC HÀNH BUỔI 9: PHP VÀ VUE.JS — UPLOAD ẢNH VÀ PHÂN TRANG SẢN PHẨM

* **Môn học:** Lập trình Web
* **Sinh viên thực hiện:** Nguyễn Việt Anh
* **Mã sinh viên:** 23001827

---

## 1. Cấu trúc thư mục bài tập (`Buoi9/`)

```text
Buoi9/
├── api/
│   ├── common.php         # Kết nối MySQL bằng PDO và hàm trả lỗi JSON
│   ├── products.php       # API CRUD và phân trang sản phẩm tại Backend
│   └── upload.php         # API kiểm tra, lưu ảnh và cập nhật đường dẫn
├── uploads/
│   ├── .gitignore         # Giữ thư mục, bỏ qua ảnh upload khi quản lý mã nguồn
│   └── .htaccess          # Chặn liệt kê thư mục và truy cập file PHP
├── Dockerfile             # Môi trường PHP có extension pdo_mysql
├── compose.yaml           # Chạy PHP và MySQL riêng bằng Docker Compose
├── .dockerignore          # Bỏ qua ảnh upload khi build image
├── start.sh               # Khởi động ứng dụng Docker
├── stop.sh                # Dừng ứng dụng Docker, giữ dữ liệu
├── database.sql           # Tạo cơ sở dữ liệu riêng, bảng products và 12 sản phẩm mẫu
├── migration.sql          # Bổ sung cột image và dữ liệu cho database CRUD cũ (tùy chọn)
├── index.html             # Giao diện biểu mẫu, bảng sản phẩm và nút phân trang
├── app.js                 # Xử lý Vue.js 3: CRUD, xem trước ảnh, upload và phân trang
└── README.md              # Tài liệu nội dung bài tập, cài đặt và kiểm thử
```

Ứng dụng chạy độc lập trong thư mục `Buoi9/`, không phụ thuộc mã nguồn của buổi trước.

---

## 2. Nội dung bài tập

### Cơ sở dữ liệu (`database.sql`)

#### Cấu trúc bảng `products`:

* `id INT`: Khóa chính, tự động tăng (`AUTO_INCREMENT PRIMARY KEY`).
* `name VARCHAR(100) NOT NULL`: Tên sản phẩm, bắt buộc nhập, tối đa 100 ký tự.
* `price INT NOT NULL`: Đơn giá (đồng), số nguyên lớn hơn 0 (`CHECK (price > 0)`).
* `stock INT NOT NULL`: Số lượng tồn, số nguyên không âm (`CHECK (stock >= 0)`).
* `image VARCHAR(255) NULL`: Đường dẫn ảnh sản phẩm; cho phép để trống.

#### Dữ liệu mẫu khởi tạo:

| Tên sản phẩm | Đơn giá (đồng) | Số lượng tồn |
| :--- | :--- | :--- |
| Bút bi | 5.000 | 20 |
| Vở ô ly | 12.000 | 15 |
| Thước kẻ | 8.000 | 0 |
| Bút chì | 6.000 | 30 |
| Tẩy gôm | 4.000 | 25 |
| Hộp bút | 25.000 | 10 |
| Bút màu | 45.000 | 12 |
| Sổ tay | 35.000 | 18 |
| Giấy A4 | 65.000 | 8 |
| Kéo học sinh | 15.000 | 14 |
| Hồ dán | 7.000 | 22 |
| Bìa hồ sơ | 3.000 | 40 |

Dữ liệu ban đầu gồm 12 sản phẩm, tương ứng 3 trang với số lượng lần lượt là **5, 5 và 2 sản phẩm**. Các sản phẩm mẫu chưa có ảnh.

---

### Câu 1: Upload ảnh sản phẩm (`api/upload.php`)

1. **Chọn và xem trước ảnh:** Biểu mẫu thêm/sửa có ô chọn một ảnh, không bắt buộc. Vue hiển thị ảnh xem trước ngay sau khi chọn.
2. **Kiểm tra tại Vue:** Chỉ chấp nhận file có đuôi JPG, JPEG hoặc PNG, MIME tương ứng và dung lượng lớn hơn 0, tối đa 2 MB (2 × 1024 × 1024 byte).
3. **Lưu thông tin trước:** Thêm/sửa thông tin vẫn gửi JSON tới `api/products.php`. Khi thêm thành công, API trả `data.id` để Vue dùng cho yêu cầu upload tiếp theo.
4. **Upload bằng FormData:** Vue gửi file và mã sản phẩm qua `multipart/form-data` tới `api/upload.php`.
5. **Kiểm tra lại tại PHP:** Kiểm tra sản phẩm tồn tại, trạng thái upload, dung lượng file thực tế, phần mở rộng và loại ảnh thực tế bằng `finfo`, `getimagesize`.
6. **Lưu ảnh:** Tạo tên file ngẫu nhiên duy nhất, lưu vào `uploads/`, sau đó cập nhật đường dẫn vào cột `image`. Chỉ thay ảnh cũ khi lưu ảnh mới và cập nhật cơ sở dữ liệu thành công.
7. **Hiển thị và thử lại:** Bảng hiển thị ảnh thu nhỏ hoặc “Chưa có ảnh”. Sửa mà không chọn ảnh mới sẽ giữ ảnh hiện tại. Nếu upload thất bại, thông tin đã lưu vẫn được giữ và xuất hiện nút **Thử lại upload ảnh**; thao tác thử lại không thêm trùng sản phẩm.

#### Thêm thông tin sản phẩm trước khi upload

* **Phương thức:** `POST`
* **Đường dẫn:** `api/products.php`
* **Dữ liệu gửi (JSON Body):**

```json
{
  "name": "Bút máy",
  "price": 45000,
  "stock": 10
}
```

* **Phản hồi thành công (200 OK, ví dụ mã mới là 13):**

```json
{
  "success": true,
  "message": "Thêm sản phẩm thành công.",
  "data": {
    "id": 13,
    "name": "Bút máy",
    "price": 45000,
    "stock": 10,
    "image": null
  }
}
```

#### Gửi ảnh cho sản phẩm đã lưu

* **Phương thức:** `POST`
* **Đường dẫn:** `api/upload.php`
* **Dữ liệu gửi (multipart/form-data):**

| Trường | Kiểu dữ liệu | Ý nghĩa |
| :--- | :--- | :--- |
| `product_id` | Số nguyên dương | Mã sản phẩm đã lưu thành công, ví dụ `13` |
| `image` | File | Một ảnh JPG, JPEG hoặc PNG, tối đa 2 MB |

* **Phản hồi thành công (200 OK, tên ảnh minh họa):**

```json
{
  "success": true,
  "message": "Upload ảnh thành công.",
  "data": {
    "id": 13,
    "image": "uploads/8f70d92a1b6c43e5a9d01234bcde5678.png"
  }
}
```

* **Phản hồi khi file giả ảnh hoặc sai loại (400 Bad Request):**

```json
{
  "success": false,
  "message": "File phải là ảnh JPG, JPEG hoặc PNG thực sự."
}
```

* **Phản hồi khi sản phẩm không tồn tại (404 Not Found):**

```json
{
  "success": false,
  "message": "Sản phẩm không tồn tại."
}
```

Các thao tác CRUD tiếp tục sử dụng `PUT api/products.php?id=...` để sửa thông tin và `DELETE api/products.php?id=...` để xóa sản phẩm. Yêu cầu sửa thông tin không ghi đè cột `image`.

---

### Câu 2: Phân trang danh sách sản phẩm (`api/products.php`)

1. **Nhận tham số:** API nhận `page` là số nguyên dương và `limit` bằng `5`. Khi thiếu tham số, dùng `page = 1`, `limit = 5`; tham số sai trả HTTP 400 cùng thông báo lỗi JSON.
2. **Phân trang tại Backend:** PHP đếm tổng sản phẩm bằng `COUNT(*)`, sắp xếp `ORDER BY id DESC`, dùng `LIMIT` và `OFFSET` để lấy đúng dữ liệu của trang yêu cầu.
3. **Trả thông tin phân trang:** JSON gồm `success`, `data`, `pagination`. Trong `pagination` có `page`, `limit`, `total`, `total_pages`; số trang làm tròn lên và tối thiểu bằng 1.
4. **Xử lý trang vượt giới hạn:** Khi `page` vượt trang cuối, API trả dữ liệu trang cuối và mã trang thực tế.
5. **Điều hướng tại Vue:** Có hai nút **Trước**, **Sau** và dòng “Trang X / Y — Tổng Z sản phẩm”. Chuyển trang gọi lại API, không tải lại toàn bộ trang. Khóa nút Trước ở trang đầu và nút Sau ở trang cuối.
6. **Cập nhật sau CRUD:** Thêm thành công tải lại trang 1; sửa/xóa tải lại trang hiện tại và sử dụng `page` thực tế API trả về.
7. **Danh sách rỗng:** Hiển thị “Chưa có sản phẩm”, tổng bằng 0, trang bằng 1 và khóa cả hai nút chuyển trang.

#### Lấy danh sách theo trang

* **Phương thức:** `GET`
* **Đường dẫn:** `api/products.php?page=3&limit=5`
* **Phản hồi thành công (200 OK, với 12 sản phẩm mẫu ban đầu):**

```json
{
  "success": true,
  "data": [
    {
      "id": 2,
      "name": "Vở ô ly",
      "price": 12000,
      "stock": 15,
      "image": null
    },
    {
      "id": 1,
      "name": "Bút bi",
      "price": 5000,
      "stock": 20,
      "image": null
    }
  ],
  "pagination": {
    "page": 3,
    "limit": 5,
    "total": 12,
    "total_pages": 3
  }
}
```

* **Phản hồi khi `page` không hợp lệ (400 Bad Request):**

```json
{
  "success": false,
  "message": "page phải là số nguyên dương."
}
```

---

### Giao diện người dùng bằng Vue.js (`index.html`, `app.js`)

1. **Biểu mẫu Thêm/Sửa:** Các trường tên, đơn giá và số lượng tồn dùng `v-model`; ô chọn ảnh dùng sự kiện `change` để kiểm tra và tạo ảnh xem trước. Bấm **Hủy bỏ** để thoát chế độ sửa.
2. **Bảng sản phẩm:** Dùng `v-for` để hiển thị mã, ảnh thu nhỏ, tên, đơn giá định dạng VND, tồn kho và các nút **Sửa**, **Xóa**.
3. **Xác nhận khi xóa:** Dùng `window.confirm(...)` trước khi gửi yêu cầu `DELETE`.
4. **Thông báo kết quả:** Hiển thị thông báo thành công hoặc lỗi; lỗi chọn ảnh nằm cạnh ô chọn file, lỗi upload có nút thử lại.
5. **Cập nhật không tải lại trang:** Sau thao tác CRUD hoặc chuyển trang, Vue gọi API để cập nhật bảng và thông tin phân trang. Các nút thao tác được khóa trong lúc lưu hoặc tải danh sách.

---

### Kiểm tra các tình huống thực nghiệm

Các tình huống dưới đây đã được kiểm tra qua API PHP/MySQL và luồng Vue; các thao tác phân trang, xem trước, thử lại upload và giữ ảnh khi sửa cũng đã được kiểm tra trên trình duyệt.

| STT | Thao tác kiểm tra | Cách thực hiện | Kết quả kiểm tra |
| :---: | :--- | :--- | :--- |
| **1** | **Upload ảnh PNG hợp lệ** | Chọn PNG nhỏ hơn 2 MB khi thêm sản phẩm, bấm **Lưu** | **Thành công:** Có ảnh xem trước và ảnh thu nhỏ đúng sản phẩm sau upload. |
| **2** | **Upload file sai loại hoặc ảnh lớn hơn 2 MB** | Chọn file sai loại/quá dung lượng; gửi trực tiếp API file giả PNG hoặc file quá 2 MB | **Thành công:** Vue báo lỗi khi chọn file; PHP từ chối yêu cầu không hợp lệ, giữ đường dẫn ảnh cũ. |
| **3** | **Sửa mà không chọn ảnh mới** | Sửa đơn giá sản phẩm có ảnh, để trống ô chọn ảnh, bấm **Lưu** | **Thành công:** Thông tin cập nhật và ảnh hiện tại vẫn được giữ. |
| **4** | **Chuyển trang với 12 sản phẩm** | Mở danh sách ban đầu, bấm **Sau** hai lần | **Thành công:** Có 3 trang, số dòng lần lượt 5, 5 và 2; các nút khóa đúng ở biên. |
| **5** | **Xóa sản phẩm duy nhất trên trang cuối** | Xóa đến khi trang cuối còn một sản phẩm, rồi xóa sản phẩm đó | **Thành công:** API trả trang hợp lệ trước đó, cập nhật tổng và số trang. |
| **6** | **Upload thất bại rồi thử lại** | Mô phỏng API upload lỗi sau khi lưu thông tin, sau đó bấm **Thử lại upload ảnh** | **Thành công:** Giữ mã và thông tin đã lưu; thử lại thành công mà không tạo trùng sản phẩm. |
| **7** | **Danh sách rỗng** | Xóa hết sản phẩm trên dữ liệu kiểm thử | **Thành công:** API trả total 0, page 1, total_pages 1; giao diện hiện “Chưa có sản phẩm” và khóa hai nút. |
| **8** | **Tham số phân trang sai hoặc vượt trang cuối** | Gửi `page=0`, `page=abc`, `limit=10` hoặc `page=999` | **Thành công:** Tham số sai trả HTTP 400; trang vượt cuối trả dữ liệu và mã trang cuối thực tế. |

---

## 3. Hướng dẫn cài đặt & chạy ứng dụng

### Cách 1: Chạy qua XAMPP (Apache + MySQL)

1. Chép thư mục dự án vào `htdocs` của XAMPP.
2. Mở **XAMPP Control Panel**, nhấn **Start** ở cả hai module **Apache** và **MySQL**.
3. Mở trình duyệt và truy cập `http://localhost/phpmyadmin/`.
4. Chọn tab **Import** (Nhập), chọn file `database.sql` trong thư mục `Buoi9/`, rồi nhấn **Import**. File tạo database riêng `buoi9_product_management` và 12 sản phẩm mẫu.
5. Đảm bảo PHP có extension `pdo_mysql`, `fileinfo` và quyền ghi vào thư mục `Buoi9/uploads/`. Nên đặt `upload_max_filesize=4M`, `post_max_size=5M` trong `php.ini`, rồi khởi động lại Apache.
6. Mở trình duyệt và truy cập:

```text
http://localhost/NguyenVietAnh_23001827/Buoi9/
```

Vue.js 3 được tải qua CDN nên trình duyệt cần kết nối Internet.

### Cách 2: Chạy nhanh bằng PHP Built-in Server

Cần cài PHP với extension `pdo_mysql`, `fileinfo` và chạy MySQL. Import `database.sql` trước khi chạy. File dành cho cài mới, không xóa bảng đang có; không import lại nếu bảng `products` đã tồn tại.

Mặc định API kết nối `127.0.0.1:3306`, cơ sở dữ liệu `buoi9_product_management`, người dùng `root`, mật khẩu rỗng. Có thể đặt các biến môi trường `DB_HOST`, `DB_PORT`, `DB_NAME`, `DB_USER`, `DB_PASS` hoặc điều chỉnh giá trị mặc định trong `api/common.php` nếu cấu hình khác.

Mở terminal tại thư mục dự án:

```bash
cd Buoi9
php -d upload_max_filesize=4M -d post_max_size=5M -S localhost:8009
```

Sau đó truy cập trình duyệt tại: `http://localhost:8009`.

### Cách 3: Chạy bằng Docker (PHP + MySQL)

Cần Docker đang hoạt động và Docker Compose. Mở terminal tại thư mục dự án:

```bash
./Buoi9/start.sh
```

Hoặc chạy trực tiếp trong thư mục `Buoi9`:

```bash
cd Buoi9
docker compose up -d --build
```

Truy cập **http://localhost:8009**. Docker tự chuẩn bị PHP, MySQL riêng và nhập `database.sql` khi volume database được tạo lần đầu. Nếu đã chạy Docker bài 9 trước đó, dữ liệu trong volume cũ sẽ được dùng lại. Cổng 8009 cần trống; mật khẩu trong `compose.yaml` dành cho thực hành cục bộ.

Dừng ứng dụng từ thư mục dự án:

```bash
./Buoi9/stop.sh
```

Lệnh dừng giữ dữ liệu MySQL trong volume `buoi9_data`. Thư mục `uploads/` được gắn trực tiếp vào container, nên ảnh vẫn nằm trên máy và được giữ khi tạo lại container. Khi sửa mã nguồn, chạy lại lệnh khởi động để build bản mới.

**Vai trò của thư mục `uploads/`:** chứa file ảnh sản phẩm được PHP lưu sau khi upload. Cơ sở dữ liệu chỉ lưu đường dẫn như `uploads/ten-anh.png` trong cột `image`; Vue dùng đường dẫn đó để hiển thị ảnh. `.gitignore` bỏ qua ảnh upload khi đưa mã lên Git; `.htaccess` hạn chế truy cập trên Apache, không được PHP Built-in Server sử dụng. Khi chuyển ứng dụng kèm dữ liệu sang máy khác, cần chép cả ảnh trong thư mục này.

### Tùy chọn: Tiếp tục cơ sở dữ liệu CRUD cũ

Chọn đúng database cũ trong phpMyAdmin, chạy `migration.sql` **một lần**, rồi cấu hình `DB_NAME` tương ứng. File bổ sung cột `image` và thêm dữ liệu mẫu cho đủ ít nhất 12 sản phẩm, giữ nguyên dữ liệu cũ. Không chạy `migration.sql` trên bảng đã tạo bằng `database.sql` của Buổi 9.

---
