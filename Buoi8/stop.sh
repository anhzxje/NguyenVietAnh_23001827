#!/bin/bash
# Dừng PHP Server cho Buoi8
docker stop buoi8-web >/dev/null 2>&1 || true
docker rm buoi8-web >/dev/null 2>&1 || true
echo "Đã dừng PHP Web Server (buoi8-web)."

