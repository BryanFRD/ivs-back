# Exécuter les commandes suivantes:

### `composer install`

Installe les dépendances du projet

### `symfony serve`

Lance le serveur sur le port 8000

# Lancer une image mysql avec docker

### `docker-compose up`

Si vous utilisez docker, cette commande créera une image de mysql

Si vous ne souhaitez pas utilisez docker, vérifiez la variable d'environnement `DATABASE_URL` dans le `.env`

# Obtenir des fake datas:

### `php bin/console doctrine:fixture:load`