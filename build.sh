#!/bin/bash

DATABASE=addressbook.db
touch $DATABASE

docker-compose up -d

docker exec -ti addr_book_php sh -c "composer install"

chmod -R 777 var/ vendor/

echo "Listening on : http:://localhost/app_dev.php/"