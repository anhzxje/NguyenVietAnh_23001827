// Fcitx 5.1.19 có thể hủy preedit trước pointerdown khi bấm chuột rời ô.
// Chỉ phục hồi bản nháp bị hủy bởi thao tác chuột, không phục hồi xóa bằng phím.
function baoVeNhapLieu(form) {
    const dangSoan = new WeakMap();
    const choKhoiPhuc = new Map();
    const laOText = el => el instanceof HTMLInputElement && el.type === 'text';
    const batDau = event => {
        if (laOText(event.target)) dangSoan.set(event.target, {
            banDau: event.target.value, ganNhat: event.target.value, xoaBangPhim: false
        });
    };
    const nhap = event => {
        const state = dangSoan.get(event.target);
        if (state && event.isComposing && event.target.value) state.ganNhat = event.target.value;
        if (!event.isComposing) choKhoiPhuc.delete(event.target);
    };
    const ketThuc = event => {
        const state = dangSoan.get(event.target);
        if (state && event.data === '' && !state.xoaBangPhim && state.ganNhat &&
            event.target.value === state.banDau && state.ganNhat !== state.banDau) {
            choKhoiPhuc.set(event.target, { value: state.ganNhat, time: Date.now() });
        }
        dangSoan.delete(event.target);
    };
    const banPhim = event => {
        choKhoiPhuc.clear();
        const state = dangSoan.get(event.target);
        if (state) state.xoaBangPhim = ['Backspace', 'Delete', 'Escape'].includes(event.key);
    };
    const bamChuot = () => {
        for (const [input, draft] of choKhoiPhuc) {
            if (Date.now() - draft.time < 500) input.value = draft.value;
        }
        choKhoiPhuc.clear();
    };
    const reset = () => {
        choKhoiPhuc.clear();
        form.querySelectorAll('input').forEach(input => dangSoan.delete(input));
    };
    const listeners = [
        [form, 'compositionstart', batDau], [form, 'input', nhap],
        [form, 'compositionend', ketThuc], [form, 'reset', reset],
        [document, 'keydown', banPhim], [document, 'pointerdown', bamChuot]
    ];
    listeners.forEach(([el, type, handler]) => el.addEventListener(type, handler, true));
    return () => listeners.forEach(([el, type, handler]) => el.removeEventListener(type, handler, true));
}

Vue.createApp({
    data() {
        return {
            sanPham: [
                { id: 1, ten: 'Bút bi xanh', gia: 5000, danhMuc: 'Văn phòng phẩm', ton: 20 },
                { id: 2, ten: 'Bút chì', gia: 8000, danhMuc: 'Văn phòng phẩm', ton: 0 },
                { id: 3, ten: 'Sổ tay', gia: 35000, danhMuc: 'Sách và vở', ton: 12 },
                { id: 4, ten: 'Vở ô ly', gia: 12000, danhMuc: 'Sách và vở', ton: 30 },
                { id: 5, ten: 'Bình nước', gia: 75000, danhMuc: 'Phụ kiện', ton: 5 },
                { id: 6, ten: 'Túi vải', gia: 45000, danhMuc: 'Phụ kiện', ton: 0 }
            ],
            danhMuc: ['Văn phòng phẩm', 'Sách và vở', 'Phụ kiện'],
            tuKhoa: '', danhMucLoc: '', sapXep: '', thongBao: '', trangThai: 'Đang tải',
            moi: { ten: '', gia: '', danhMuc: 'Văn phòng phẩm', ton: '' }, loi: '', thanhCong: ''
        };
    },
    computed: {
        sanPhamHienThi() {
            const tuKhoa = this.tuKhoa.trim().toLocaleLowerCase('vi');
            const ketQua = this.sanPham.filter(sp => sp.ten.toLocaleLowerCase('vi').includes(tuKhoa) && (!this.danhMucLoc || sp.danhMuc === this.danhMucLoc));
            if (this.sapXep) ketQua.sort((a, b) => this.sapXep === 'tang' ? a.gia - b.gia : b.gia - a.gia);
            return ketQua;
        },
        soLuongHienThi() { return this.sanPhamHienThi.length; },
        tongSoSanPham() { return this.sanPham.length; },
        tongGiaTri() { return this.sanPhamHienThi.reduce((tong, sp) => tong + sp.gia * sp.ton, 0); }
    },
    watch: {
        tuKhoa(giaTri) { this.thongBao = giaTri.length === 1 ? 'Hãy nhập thêm ký tự để tìm kiếm.' : ''; }
    },
    mounted() {
        this.huyBaoVeNhapLieu = baoVeNhapLieu(this.$refs.formSanPham);
        this.trangThai = 'Sẵn sàng';
    },
    beforeUnmount() { this.huyBaoVeNhapLieu(); },
    methods: {
        tien(n) { return n.toLocaleString('vi-VN') + ' đồng'; },
        themSanPham(event) {
            const form = event?.currentTarget;
            // Đọc đúng nội dung đang hiện trong form, không phụ thuộc sự kiện
            // input/composition của bộ gõ hay thao tác chuột khi chuyển ô.
            if (form) this.moi = Object.fromEntries(new FormData(form));
            const { ten } = this.moi;
            const tonNhap = String(this.moi.ton).trim();
            const ton = /^\d+$/.test(tonNhap) ? Number(tonNhap) : NaN;
            const giaNhap = this.moi.gia;
            const gia = typeof giaNhap === 'string' && giaNhap.trim() === '' ? NaN : Number(giaNhap);
            const danhMuc = this.moi.danhMuc === '__moi__' ? (this.moi.danhMucMoi || '') : this.moi.danhMuc;
            this.thanhCong = '';
            if (!ten.trim() || typeof gia !== 'number' || !Number.isFinite(gia) || gia <= 0 || !Number.isInteger(ton) || ton < 0 || !danhMuc.trim()) {
                this.loi = 'Nhập tên và danh mục, giá lớn hơn 0, tồn kho là số nguyên không âm.'; return;
            }
            const tenChuan = ten.trim().replace(/\s+/g, ' ').toLocaleLowerCase('vi');
            if (this.sanPham.some(sp => sp.ten.trim().replace(/\s+/g, ' ').toLocaleLowerCase('vi') === tenChuan)) {
                this.loi = 'Tên sản phẩm đã tồn tại. Vui lòng nhập tên khác.'; return;
            }
            const danhMucChuan = this.danhMuc.find(dm => dm.toLocaleLowerCase('vi') === danhMuc.trim().toLocaleLowerCase('vi')) || danhMuc.trim();
            if (!this.danhMuc.includes(danhMucChuan)) this.danhMuc.push(danhMucChuan);
            this.sanPham.push({ id: Math.max(0, ...this.sanPham.map(sp => sp.id)) + 1, ten: ten.trim(), gia, danhMuc: danhMucChuan, ton });
            this.tuKhoa = ''; this.danhMucLoc = '';
            if (form) form.reset();
            this.moi = { ten: '', gia: '', danhMuc: 'Văn phòng phẩm', ton: '' }; this.loi = '';
            this.thanhCong = 'Đã thêm sản phẩm ' + ten.trim() + '. Danh sách đã được cập nhật.';
        },
        xoaSanPham(id) { if (window.confirm('Bạn có chắc muốn xóa sản phẩm này?')) this.sanPham = this.sanPham.filter(sp => sp.id !== id); }
    }
}).mount('#app');
