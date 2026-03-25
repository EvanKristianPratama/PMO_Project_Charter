#!/bin/sh
set -e

APP_DIR="/var/www/html"

mkdir -p \
    "${APP_DIR}/storage/framework/cache" \
    "${APP_DIR}/storage/framework/sessions" \
    "${APP_DIR}/storage/framework/views" \
    "${APP_DIR}/storage/logs" \
    "${APP_DIR}/bootstrap/cache"

chown -R www-data:www-data "${APP_DIR}/storage" "${APP_DIR}/bootstrap/cache"
chmod -R ug+rwx "${APP_DIR}/storage" "${APP_DIR}/bootstrap/cache"

exec "$@"
