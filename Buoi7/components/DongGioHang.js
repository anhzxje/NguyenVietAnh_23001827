const DongGioHang = {
    props: { sanPham: { type: Object, required: true } },
    emits: ['doi-so-luong', 'yeu-cau-xoa'],
    template: `<tr><td>{{ sanPham.ten }}</td><td>{{ sanPham.gia.toLocaleString('vi-VN') }} đồng</td>
        <td><div class="quantity"><button type="button" class="secondary" :disabled="sanPham.soLuong <= 1" @click="$emit('doi-so-luong', sanPham.id, sanPham.soLuong - 1)" :aria-label="'Giảm số lượng ' + sanPham.ten">−</button><span>{{ sanPham.soLuong }}</span><button type="button" class="secondary" :disabled="sanPham.soLuong >= 99" @click="$emit('doi-so-luong', sanPham.id, sanPham.soLuong + 1)" :aria-label="'Tăng số lượng ' + sanPham.ten">+</button></div></td>
        <td>{{ (sanPham.gia * sanPham.soLuong).toLocaleString('vi-VN') }} đồng</td><td><button type="button" class="danger" @click="$emit('yeu-cau-xoa', sanPham.id)">Xóa</button></td></tr>`
};
