composer install
yarn install
yarn run encore dev
setfacl -R  -m u:www-data:rwX -m u:root:rwX var
setfacl -dR -m u:www-data:rwX -m u:root:rwX var
#composer install --working-dir=tools/php-cs-fixer
#composer install --working-dir=tools/php-stan
symfony console doctrine:migration:migrate -n
symfony console app:fixtures:load -n
symfony local:server:stop --dir=/var/www/public
symfony console assets:install
symfony local:server:start --dir=/var/www/public --no-tls
