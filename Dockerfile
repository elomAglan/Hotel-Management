# Utilise une image de base officielle
FROM php:7.4-apache

# Copier les fichiers de votre projet dans le conteneur
COPY . /var/www/html/

# Exposer le port 80
EXPOSE 80
