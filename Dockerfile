# Utiliser PHP 8.2 CLI officiel
FROM php:8.2-cli

# Définir le dossier de travail
WORKDIR /app

# Copier tout le contenu du dépôt dans le container
COPY . .

# Lancer le serveur PHP sur le port fourni par Render
CMD ["php", "-S", "0.0.0.0:10000"]
