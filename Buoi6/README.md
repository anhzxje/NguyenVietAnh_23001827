# Bài thực hành số 6 — VueJS

## Bài 1: Thẻ thông tin sinh viên

Mở `Bai1.html` bằng trình duyệt hoặc truy cập qua Apache/XAMPP:

```text
http://localhost/NguyenVietAnh_23001827/Buoi6/Bai1.html
```

Cần kết nối Internet để tải Vue 3 từ CDN. Không cần cài Node.js hay chạy build.
Cách dùng Vue trực tiếp qua CDN: [tài liệu Vue](https://vuejs.org/guide/quick-start.html#using-vue-from-cdn).

- `app.js`: `createApp`, `data()`, dữ liệu mẫu và màu xếp loại.
- `Bai1.html`: họ tên dùng `{{ }}`, lớp dùng `v-text`, ảnh dùng `v-bind:src` và `v-bind:alt`.
- Xếp loại và trạng thái dùng `v-if`/`v-else-if`/`v-else`; màu dùng `:class`.
- Nút giới thiệu dùng `v-show`; các ô nhập dùng `v-model` để thử thay đổi dữ liệu.
- Điểm rỗng hoặc ngoài khoảng 0–10 được báo không hợp lệ, không được xếp loại.

Thử điểm `0`, `4.9`, `5`, `6.4`, `6.5`, `7.9`, `8`, `10` để kiểm tra các mốc.
Bỏ chọn “Sinh viên đang học” và bấm “Ẩn giới thiệu” để kiểm tra trạng thái và `v-show`.
Thông tin lớp và điểm là dữ liệu mẫu; thay trực tiếp ở giao diện hoặc trong `data()`.
Nhấn **Lưu thông tin** để lưu hồ sơ bằng `localStorage` trên trình duyệt hiện tại.
Khi tải lại trang, hồ sơ đã lưu được khôi phục; thay đổi chưa lưu chỉ dùng để xem trước.
Nút lưu bị vô hiệu hóa nếu mã sinh viên, họ tên, lớp rỗng hoặc điểm không hợp lệ.

## Bài 2: Đặt vé xem phim

Mở `Bai2.html`. Logic nằm trong `bai2.js`, dùng `v-model.trim` cho họ tên,
`v-model.number` cho số lượng, radio cho loại vé và checkbox cho combo.
Các giá trị `computed` kiểm tra số nguyên từ 1 đến 10, tính giá vé, tiền combo và tổng tiền.
Form dùng `@submit.prevent` để hiển thị xác nhận mà không tải lại trang;
`@reset.prevent` gọi method đặt lại toàn bộ form và xóa xác nhận.

Kiểm tra: nhập họ tên, chọn VIP, số lượng 2 và combo → **245.000 đồng**.
Thử tên chỉ có khoảng trắng hoặc số lượng rỗng, 0, 11, 1.5 → nút Đặt vé bị vô hiệu hóa.
Nút tăng/giảm dừng tại 1 và 10. Thông tin xác nhận giữ nguyên khi sửa form sau khi đặt vé.

## Bài 3: Quản lý công việc cá nhân

Mở `Bai3.html`. Logic nằm trong `bai3.js`, mỗi việc gồm `id`, `ten`, `hoanThanh`.
Thêm bằng nút hoặc Enter qua `@submit.prevent`; không thêm tên rỗng.
Danh sách dùng `v-for` với `:key="viec.id"`. Checkbox cập nhật trạng thái và gạch ngang tên.
Các `computed` lọc công việc, đếm tổng số, số việc chưa hoàn thành và đã hoàn thành.

Kiểm tra: thêm 2 việc, hoàn thành 1 việc → tổng 2, chưa hoàn thành 1, đã hoàn thành 1.
Thử cả 3 bộ lọc, xóa từng việc và xóa tất cả việc đã hoàn thành.
Khi danh sách hoặc bộ lọc không có kết quả, trang hiển thị thông báo tương ứng.

Cả ba bài dùng chung `style.css`, thanh chuyển bài, độ rộng trang, kiểu chữ, màu sắc,
ô nhập, nút bấm và chân trang. Thanh chuyển bài đánh dấu bài hiện tại và tự điều chỉnh trên điện thoại.
