const { createApp } = Vue;

createApp({
    data() {
        return {
            apiUrl: 'api/products.php',
            products: [],
            pagination: { page: 1, limit: 5, total: 0, total_pages: 1 },
            form: { id: null, name: '', price: '', stock: '', image: null },
            isEditing: false,
            selectedImage: null,
            previewUrl: '',
            imageError: '',
            uploadError: '',
            uploadReturnPage: 1,
            loading: false,
            submitting: false,
            notification: { text: '', type: 'success' }
        };
    },
    mounted() { this.fetchProducts(1); },
    beforeUnmount() { this.clearPreview(); },
    methods: {
        showNotification(text, type = 'success') {
            this.notification = { text, type };
        },
        async readResponse(response) {
            const result = await response.json();
            if (!response.ok || !result.success) throw new Error(result.message || 'Yêu cầu không thành công.');
            return result;
        },
        async fetchProducts(page = this.pagination.page) {
            this.loading = true;
            try {
                const result = await this.readResponse(await fetch(`${this.apiUrl}?page=${page}&limit=5`, {
                    headers: { Accept: 'application/json' }
                }));
                this.products = result.data;
                this.pagination = result.pagination;
            } catch (error) {
                this.showNotification('Không thể tải danh sách: ' + error.message, 'error');
            } finally {
                this.loading = false;
            }
        },
        clearPreview() {
            if (this.previewUrl.startsWith('blob:')) URL.revokeObjectURL(this.previewUrl);
            this.previewUrl = '';
        },
        selectImage(event) {
            this.clearPreview();
            this.selectedImage = null;
            this.imageError = '';
            this.previewUrl = this.form.image || '';
            const file = event.target.files[0];
            if (!file) return;
            if (!/\.(jpe?g|png)$/i.test(file.name) || !['image/jpeg', 'image/png'].includes(file.type)) {
                this.imageError = 'Chỉ chấp nhận ảnh JPG, JPEG hoặc PNG.';
            } else if (file.size === 0 || file.size > 2 * 1024 * 1024) {
                this.imageError = 'Ảnh phải lớn hơn 0 và tối đa 2 MB.';
            }
            if (this.imageError) {
                event.target.value = '';
                return;
            }
            this.selectedImage = file;
            this.previewUrl = URL.createObjectURL(file);
        },
        async uploadImage() {
            const body = new FormData();
            body.append('product_id', String(this.form.id));
            body.append('image', this.selectedImage);
            const result = await this.readResponse(await fetch('api/upload.php', { method: 'POST', body }));
            this.form.image = result.data.image;
            this.uploadError = '';
        },
        async saveProduct() {
            if (this.submitting || this.imageError) return;
            const payload = {
                name: this.form.name.trim(),
                price: Number(this.form.price),
                stock: Number(this.form.stock)
            };
            if (!payload.name || [...payload.name].length > 100
                || this.form.price === '' || !Number.isInteger(payload.price) || payload.price < 1 || payload.price > 2147483647
                || this.form.stock === '' || !Number.isInteger(payload.stock) || payload.stock < 0 || payload.stock > 2147483647) {
                this.showNotification('Kiểm tra tên (1–100 ký tự), đơn giá và số lượng tồn hợp lệ.', 'error');
                return;
            }
            const adding = !this.isEditing;
            this.uploadReturnPage = adding ? 1 : this.pagination.page;
            this.submitting = true;
            this.uploadError = '';
            try {
                const url = adding ? this.apiUrl : `${this.apiUrl}?id=${this.form.id}`;
                const result = await this.readResponse(await fetch(url, {
                    method: adding ? 'POST' : 'PUT',
                    headers: { 'Content-Type': 'application/json', Accept: 'application/json' },
                    body: JSON.stringify(payload)
                }));
                // Lưu ngay mã mới: upload lỗi không được làm lần Lưu sau tạo sản phẩm trùng.
                this.form.id = result.data.id;
                this.isEditing = true;
                if (this.selectedImage) {
                    try {
                        await this.uploadImage();
                    } catch (error) {
                        this.uploadError = error.message;
                        this.showNotification('Thông tin sản phẩm đã lưu. Upload ảnh thất bại; có thể thử lại bên dưới.', 'error');
                        await this.fetchProducts(this.uploadReturnPage);
                        return;
                    }
                }
                this.resetForm();
                this.showNotification(adding ? 'Thêm sản phẩm thành công.' : 'Cập nhật sản phẩm thành công.');
                await this.fetchProducts(this.uploadReturnPage);
            } catch (error) {
                this.showNotification('Không lưu được sản phẩm: ' + error.message, 'error');
            } finally {
                this.submitting = false;
            }
        },
        async retryUpload() {
            if (this.submitting || !this.selectedImage || !this.form.id || this.imageError) return;
            this.submitting = true;
            try {
                await this.uploadImage();
                this.resetForm();
                this.showNotification('Upload ảnh thành công.');
                await this.fetchProducts(this.uploadReturnPage);
            } catch (error) {
                this.uploadError = error.message;
                this.showNotification('Thông tin sản phẩm vẫn được giữ. Upload ảnh thất bại.', 'error');
            } finally {
                this.submitting = false;
            }
        },
        startEdit(product) {
            if (this.submitting || this.loading) return;
            this.resetForm();
            this.isEditing = true;
            this.form = { ...product };
            this.previewUrl = product.image || '';
            this.$nextTick(() => this.$refs.nameInput?.focus());
        },
        cancelEdit() { if (!this.submitting) this.resetForm(); },
        resetForm() {
            this.clearPreview();
            this.selectedImage = null;
            this.imageError = '';
            this.uploadError = '';
            this.isEditing = false;
            this.form = { id: null, name: '', price: '', stock: '', image: null };
            if (this.$refs.imageInput) this.$refs.imageInput.value = '';
        },
        async deleteProduct(product) {
            if (this.submitting || this.loading || !window.confirm(`Xóa sản phẩm "${product.name}" (mã #${product.id})?`)) return;
            this.submitting = true;
            try {
                await this.readResponse(await fetch(`${this.apiUrl}?id=${product.id}`, { method: 'DELETE' }));
                if (this.form.id === product.id) this.resetForm();
                this.showNotification('Xóa sản phẩm thành công.');
                await this.fetchProducts(this.pagination.page);
            } catch (error) {
                this.showNotification('Không xóa được sản phẩm: ' + error.message, 'error');
            } finally {
                this.submitting = false;
            }
        },
        formatCurrency(amount) {
            return new Intl.NumberFormat('vi-VN').format(amount) + ' đ';
        }
    }
}).mount('#app');
