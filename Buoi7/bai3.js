Vue.createApp({
    data() { return { gioHang: [
        { id: 1, ten: 'Bút', gia: 5000, soLuong: 2 },
        { id: 2, ten: 'Vở', gia: 12000, soLuong: 3 }
    ] }; },
    computed: {
        tongSoLuong() { return this.gioHang.reduce((tong, sp) => tong + sp.soLuong, 0); },
        tongTien() { return this.gioHang.reduce((tong, sp) => tong + sp.gia * sp.soLuong, 0); }
    },
    methods: {
        doiSoLuong(id, soLuong) {
            if (!Number.isInteger(soLuong) || soLuong < 1 || soLuong > 99) return;
            const sp = this.gioHang.find(item => item.id === id);
            if (sp) sp.soLuong = soLuong;
        },
        xoaSanPham(id) { if (this.gioHang.some(sp => sp.id === id)) this.gioHang = this.gioHang.filter(sp => sp.id !== id); }
    }
}).component('dong-gio-hang', DongGioHang).mount('#app');
