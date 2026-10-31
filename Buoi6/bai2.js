const { createApp } = Vue;

createApp({
    data() {
        return {
            tenPhim: 'Hành trình đến những vì sao',
            cacSuatChieu: ['14:00', '17:30', '20:00'],
            hoTen: '',
            suatChieu: '14:00',
            loaiVe: 'thuong',
            soLuong: 1,
            coCombo: false,
            xacNhan: null
        };
    },
    computed: {
        giaVe() {
            return this.loaiVe === 'vip' ? 100000 : 70000;
        },
        tienCombo() {
            return this.coCombo ? 45000 : 0;
        },
        soLuongHopLe() {
            return Number.isInteger(this.soLuong) && this.soLuong >= 1 && this.soLuong <= 10;
        },
        tongTien() {
            return this.soLuongHopLe ? this.giaVe * this.soLuong + this.tienCombo : 0;
        },
        coTheDatVe() {
            return this.hoTen.trim().length > 0 && this.soLuongHopLe;
        }
    },
    methods: {
        dinhDangTien(soTien) {
            return soTien.toLocaleString('vi-VN') + ' đồng';
        },
        tangSoLuong() {
            if (this.soLuongHopLe && this.soLuong < 10) this.soLuong++;
        },
        giamSoLuong() {
            if (this.soLuongHopLe && this.soLuong > 1) this.soLuong--;
        },
        datLai() {
            this.hoTen = '';
            this.suatChieu = this.cacSuatChieu[0];
            this.loaiVe = 'thuong';
            this.soLuong = 1;
            this.coCombo = false;
            this.xacNhan = null;
        },
        datVe() {
            if (!this.coTheDatVe) return;
            // Lưu bản chụp đơn vé để việc sửa form không đổi đơn đã xác nhận.
            this.xacNhan = {
                hoTen: this.hoTen.trim(),
                tenPhim: this.tenPhim,
                suatChieu: this.suatChieu,
                loaiVe: this.loaiVe === 'vip' ? 'vé VIP' : 'vé thường',
                soLuong: this.soLuong,
                coCombo: this.coCombo,
                tongTien: this.tongTien
            };
        }
    }
}).mount('#app');
