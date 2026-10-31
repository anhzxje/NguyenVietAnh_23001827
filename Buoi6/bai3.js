const { createApp } = Vue;

createApp({
    data() {
        return {
            congViec: [],
            tenCongViec: '',
            idTiepTheo: 1,
            boLoc: 'tat-ca',
            cacBoLoc: [
                { giaTri: 'tat-ca', nhan: 'Tất cả' },
                { giaTri: 'chua-xong', nhan: 'Chưa hoàn thành' },
                { giaTri: 'da-xong', nhan: 'Đã hoàn thành' }
            ]
        };
    },
    computed: {
        tongCongViec() {
            return this.congViec.length;
        },
        soViecChuaHoanThanh() {
            return this.congViec.filter(viec => !viec.hoanThanh).length;
        },
        soViecDaHoanThanh() {
            return this.tongCongViec - this.soViecChuaHoanThanh;
        },
        congViecDaLoc() {
            if (this.boLoc === 'chua-xong') return this.congViec.filter(viec => !viec.hoanThanh);
            if (this.boLoc === 'da-xong') return this.congViec.filter(viec => viec.hoanThanh);
            return this.congViec;
        }
    },
    methods: {
        themCongViec() {
            const ten = this.tenCongViec.trim();
            if (!ten) return;
            this.congViec.push({ id: this.idTiepTheo++, ten, hoanThanh: false });
            this.tenCongViec = '';
        },
        xoaCongViec(id) {
            this.congViec = this.congViec.filter(viec => viec.id !== id);
        },
        xoaDaHoanThanh() {
            this.congViec = this.congViec.filter(viec => !viec.hoanThanh);
        }
    }
}).mount('#app');
