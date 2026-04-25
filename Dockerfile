FROM php:8.2-apache

# Instala dependências e extensões
RUN apt-get update && apt-get install -y \
    libldap2-dev libicu-dev \
    && docker-php-ext-configure ldap --with-libdir=lib/x86_64-linux-gnu/ \
    && docker-php-ext-install ldap pdo pdo_mysql mysqli intl

# Habilita mod_rewrite
RUN a2enmod rewrite

# Configura o DocumentRoot
ENV APACHE_DOCUMENT_ROOT /var/www/html/public
RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/sites-available/*.conf
RUN sed -ri -e 's!/var/www/!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/apache2.conf

# --- CORREÇÃO DO 404: Permitir .htaccess ---
# Cria uma configuração extra para garantir que o AllowOverride funcione
RUN echo "<Directory /var/www/html/public>" > /etc/apache2/conf-available/ci4-override.conf \
    && echo "    Options Indexes FollowSymLinks" >> /etc/apache2/conf-available/ci4-override.conf \
    && echo "    AllowOverride All" >> /etc/apache2/conf-available/ci4-override.conf \
    && echo "    Require all granted" >> /etc/apache2/conf-available/ci4-override.conf \
    && echo "</Directory>" >> /etc/apache2/conf-available/ci4-override.conf \
    && a2enconf ci4-override

# Reinicia o Apache (opcional, pois o container faz isso no start)
RUN service apache2 restart
