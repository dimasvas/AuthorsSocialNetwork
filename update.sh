#!/bin/bash

# Commands
#
##Clear Symfony Cache
# app/console cache:clear
#
##Dump Assetic Assets
#app/console assetic:dump
#

function rights {

    echo "Setting rights"

#    chown  -R www-data:www-data var/
#    chown  -R www-data:www-data web/
#    chmod -R 700 var/logs
#    chmod -R 700 var/cache
#    chmod -R 700 var/sessions
#    chmod -R 644 web/media
#    chmod -R 644 web/tmp
#    chmod -R 644 web/uploads
}

function production {

    echo "Clear PRODUCTION enviroment"
  
    export SYMFONY_ENV=prod

    bin/console cache:clear -e=prod
    bin/console assetic:dump --env=prod
    bin/console assets:install --symlink web
    bin/console fos:js-routing:dump --env=prod
}

function development {

    echo "Clear DEVELOPMENT enviroment"

    bin/console cache:clear --env=dev
    bin/console assetic:dump
    bin/console assets:install web
    bin/console cache:clear
}

function database {

    echo "Clear doctrine cache"

    bin/console doctrine:cache:clear-metadata
    bin/console doctrine:cache:clear-query
    bin/console doctrine:cache:clear-result
}

function jsroutes {
   echo "Dump JS routes"

   bin/console fos:js-routing:dump
}

function dirRights {

    echo 'Setting dir rights to RW: web/media/*';

    chmod -R 666 web/media/ 
}

rights

case $1 in
    "dev")
        development
        ;;
    "prod")
        production
        ;;
    "doctrine")
        database
        ;;
    "route")
        jsroutes
        ;;
    "dir")
        dirRights
        ;;    
    *)
        echo "Wrong parametres. Use:   dev | prod | doctrine"
        ;;
esac

rights
