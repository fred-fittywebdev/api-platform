# Pour Symfony 5 
# Ce script shell permet de supprimer l'ensemble des fichiers de migrations de votre projet, créer leur repertoire si nécessaire puis il réinitialise l'ensemble de la base de donnée utilisée par doctrine (configuré dans .env ou .env.local).
# Pour l'executer, il faut être à la racine du projet (là ou le script doit également être placé) et executer la commande : ./init-db.sh 
# Lors de sa première execution vous n'aurez probablement pas les droits d'execution, executez : chmod +x ./init-db.sh puis relancez le script.

if [ migrations/ ]; then
    rm -rf migrations/*;
    echo "migrations directory purged";
else
    mkdir migrations;
    echo "migrations directory created";
fi

php bin/console d:d:d --force;
php bin/console d:d:c;
php bin/console make:migration;
php bin/console d:m:m;
php bin/console d:f:l;

echo "Bravo, la database a été réinitialisée"