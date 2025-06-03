FROM php:8.2-apache

# Instala o mysqli e pdo_mysql
RUN docker-php-ext-install mysqli pdo pdo_mysql

# Habilita o módulo rewrite do Apache
RUN a2enmod rewrite

# Copia os arquivos da aplicação para o diretório do Apache
COPY . /var/www/html/

# Ajusta permissões
RUN chown -R www-data:www-data /var/www/html

ENV DB_HOST=db
ENV DB_USER=admin
ENV DB_PASSWORD=admin
ENV DB_NAME=admin
