#!/usr/bin/env bash
set -euo pipefail
cd -- "$(dirname -- "${BASH_SOURCE[0]}")"
docker compose up -d --build
printf 'Buổi 9: http://localhost:8009\n'
