# Bài thực hành buổi 7 — Vue 3

## Cách chạy

Mở `Bai1.html`, `Bai2.html` hoặc `Bai3.html` bằng trình duyệt. Cần kết nối Internet để tải Vue 3 từ CDN; không cần cài đặt hoặc build. Dữ liệu mẫu được đặt lại khi tải lại trang.

## Bài 1: Quản lý sản phẩm

Tìm kiếm theo tên, lọc theo danh mục và sắp xếp theo giá. Thống kê số sản phẩm và giá trị tồn kho thay đổi theo danh sách đang hiển thị.

Có thể thêm sản phẩm và danh mục mới, hoặc xóa sản phẩm sau khi xác nhận. Tên không được để trống hoặc trùng với sản phẩm đã có; giá phải lớn hơn 0 và tồn kho phải là số nguyên không âm. Nhập giá bằng chữ số, ví dụ `50000`.

## Bài 2: Danh mục sản phẩm

Component `components/TheSanPham.js` nhận dữ liệu sản phẩm qua props. Mỗi thẻ có thể bật hoặc tắt mô tả riêng; sản phẩm hết hàng được gắn nhãn và khóa nút Mua.

## Bài 3: Giỏ hàng

Component `components/DongGioHang.js` phát sự kiện để component cha tăng, giảm hoặc xóa sản phẩm. Số lượng mỗi sản phẩm từ 1 đến 99.

Dữ liệu mẫu gồm 2 bút giá 5.000 đồng và 3 quyển vở giá 12.000 đồng: tổng số lượng là **5**, tổng tiền là **46.000 đồng**. Khi xóa hết sản phẩm, trang hiển thị “Giỏ hàng trống”.

## Kiểm tra

Từ thư mục gốc của dự án, chạy:

```bash
node Buoi7/test.js
```
