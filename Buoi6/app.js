const { createApp } = Vue;

createApp({
    data() {
        return {
            sinhVien: {
                maSinhVien: '23001827',
                hoTen: 'Nguyễn Việt Anh',
                lop: 'CNTT',
                anhDaiDien: 'avatar.svg',
                diemTrungBinh: 8.5,
                dangHoc: true,
                gioiThieu: 'Xin chào! Mình là Nguyễn Việt Anh, sinh viên yêu thích công nghệ và lập trình web. Mình đang tìm hiểu cách xây dựng giao diện tương tác với VueJS.'
            },
            hienGioiThieu: true,
            banDaLuu: null,
            loiLuu: ''
        };
    },
    computed: {
        coTheLuu() {
            return this.diemHopLe && ['maSinhVien', 'hoTen', 'lop'].every(truong => this.sinhVien[truong].trim());
        },
        coThayDoi() {
            return JSON.stringify(this.sinhVien) !== this.banDaLuu;
        },
        diemHopLe() {
            const diem = this.sinhVien.diemTrungBinh;
            return typeof diem === 'number' && Number.isFinite(diem) && diem >= 0 && diem <= 10;
        },
        mauXepLoai() {
            if (!this.diemHopLe) return 'chua-co-diem';
            const diem = this.sinhVien.diemTrungBinh;
            if (diem >= 8) return 'gioi';
            if (diem >= 6.5) return 'kha';
            if (diem >= 5) return 'trung-binh';
            return 'chua-dat';
        }
    },
    created() {
        try {
            const duLieu = localStorage.getItem('buoi6.bai1.sinhVien');
            if (!duLieu) return;
            const sinhVien = JSON.parse(duLieu);
            const cacTruong = Object.keys(this.sinhVien);
            const dungKieu = sinhVien && cacTruong.every(truong => typeof sinhVien[truong] === typeof this.sinhVien[truong]);
            if (!dungKieu || !Number.isFinite(sinhVien.diemTrungBinh) || sinhVien.diemTrungBinh < 0 || sinhVien.diemTrungBinh > 10
                || !['maSinhVien', 'hoTen', 'lop'].every(truong => sinhVien[truong].trim())) {
                throw new Error('Dữ liệu đã lưu không hợp lệ');
            }
            cacTruong.forEach(truong => { this.sinhVien[truong] = sinhVien[truong]; });
            this.banDaLuu = JSON.stringify(this.sinhVien);
        } catch {
            this.loiLuu = 'Không thể đọc thông tin đã lưu. Bạn vẫn có thể chỉnh sửa và thử lưu lại.';
        }
    },
    methods: {
        luuThongTin() {
            if (!this.coTheLuu) return;
            try {
                const duLieu = JSON.stringify(this.sinhVien);
                localStorage.setItem('buoi6.bai1.sinhVien', duLieu);
                this.banDaLuu = duLieu;
                this.loiLuu = '';
            } catch {
                this.loiLuu = 'Không thể lưu thông tin. Vui lòng kiểm tra quyền lưu dữ liệu của trình duyệt và thử lại.';
            }
        },
        dungAnhMacDinh(event) {
            // Chỉ thay ảnh một lần để tránh lặp nếu ảnh mặc định bị thiếu.
            if (event.target.getAttribute('src') !== 'avatar.svg') {
                event.target.src = 'avatar.svg';
            }
        }
    }
}).mount('#app');
