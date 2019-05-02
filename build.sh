#!/bin/bash

DATABASE=addressbook.sqlite
touch database/$DATABASE

docker-compose up -d

docker exec -ti addr_book_php sh -c "composer install"
docker exec -ti addr_book_php sh -c "composer dump-autoload"
docker exec -ti addr_book_php sh -c "php bin/console assets:install"
docker exec -ti addr_book_php sh -c "php bin/console d:s:u --force --dump-sql"
docker exec -ti addr_book_php sh -c "php bin/console ca:cl"
mkdir web/uploads

chmod -R 777 var/ vendor/ $DATABASE web/uploads app/config/parameters.yml database/

echo "Listening on : http:://localhost/app_dev.php/"