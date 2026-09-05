# ==============================================================
# API Facturas v1 — PHP puro (puerto 8022)
# El servidor embebido de PHP con index.php como router: todas
# las peticiones pasan por el front controller sin configurar
# Apache/nginx. En desarrollo, el compose monta el código como
# volumen: guardar un .php = refrescar (PHP reinterpreta).
# ==============================================================
FROM php:8.3-cli

# La extensión PDO para MySQL/MariaDB se compila en el build:
RUN docker-php-ext-install pdo_mysql

WORKDIR /app

# El código (en desarrollo queda tapado por el volumen del compose)
COPY . .

EXPOSE 8026

CMD ["php", "-S", "0.0.0.0:8026", "index.php"]
