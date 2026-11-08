const TheSanPham = {
    props: {
        ten: { type: String, required: true },
        gia: { type: Number, required: true },
        anh: { type: String, default: '' },
        moTa: { type: String, default: 'Chưa có mô tả.' },
        ton: { type: Number, default: 0 }
    },
    data() { return { hienMoTa: false }; },
    template: `
        <article class="card product-card">
            <img v-if="anh" :src="anh" :alt="ten" class="product-image">
            <div class="product-body">
                <span class="badge" :class="ton > 0 ? 'available' : 'sold-out'">{{ ton > 0 ? 'Còn hàng' : 'Hết hàng' }}</span>
                <h2>{{ ten }}</h2><p class="price">{{ gia.toLocaleString('vi-VN') }} đồng</p>
                <p>Còn {{ ton }} sản phẩm</p>
                <p v-if="hienMoTa">{{ moTa }}</p>
                <div class="actions"><button type="button" class="secondary" @click="hienMoTa = !hienMoTa" :aria-expanded="hienMoTa">{{ hienMoTa ? 'Ẩn mô tả' : 'Xem mô tả' }}</button><button type="button" :disabled="ton <= 0">Mua</button></div>
            </div>
        </article>`
};
