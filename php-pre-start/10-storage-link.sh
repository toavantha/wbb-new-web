#!/bin/bash
set -e

cd /opt/app-root/src

mkdir -p storage/app/public

php artisan storage:link

echo "Laravel public storage link verified."
