Vue.createApp({
    data() { return { sanPham: [
        { id: 1, ten: 'Bút bi xanh', gia: 5000, anh: 'images/but.svg', moTa: 'Bút viết mực xanh, phù hợp để ghi chép hằng ngày.', ton: 20 },
        { id: 2, ten: 'Vở ô ly', gia: 12000, anh: 'images/vo.svg', moTa: 'Vở ô ly với giấy sáng, dễ viết.', ton: 15 },
        { id: 3, ten: 'Sổ tay', gia: 35000, anh: 'images/so.svg', moTa: 'Sổ nhỏ gọn để lưu lại ý tưởng.', ton: 0 },
        { id: 4, ten: 'Bình nước', gia: 75000, anh: 'images/binh.svg', moTa: 'Bình nước tiện mang theo khi đi học.', ton: 5 }
    ] }; }
}).component('the-san-pham', TheSanPham).mount('#app');
