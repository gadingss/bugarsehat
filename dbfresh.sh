docker exec -it bugarsehat-app-live bash -c "cd /var/www/html/bugarsehat \
    && php artisan key:generate \
    && php artisan jwt:secret -f \
    && php artisan migrate:fresh \
    && php artisan db:seed \
    && php artisan optimize:clear"
