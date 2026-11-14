#!/bin/bash
# Khởi động dịch vụ MySQL và PHP Server cho Buoi8

echo "1. Đang khởi động MySQL..."
docker start mysql-mysql-1 >/dev/null 2>&1 || true

echo "2. Đang khởi động PHP Web Server..."
docker rm -f buoi8-web >/dev/null 2>&1 || true
docker run -d --name buoi8-web --network host \
  -e DB_HOST=127.0.0.1 \
  -e DB_PORT=3306 \
  -e DB_USER=root \
  -e DB_PASS=root \
  -e DB_NAME=product_management \
  -v "$(pwd)":/var/www/html \
  buoi8-php >/dev/null

echo "=========================================================="
echo "Ứng dụng đã sẵn sàng!"
echo "Truy cập giao diện tại: http://localhost:8000"
echo "API Backend tại:        http://localhost:8000/api/products.php"
echo "=========================================================="

