#!/bin/sh

if nc -z db 3306 -w 600;
  then
    bin/console doctrine:database:create --if-not-exists
    bin/console doctrine:migrations:migrate -n
    bin/console cache:clear
    bin/console cache:warmup
  else
    echo "CAN NOT CONNECT TO MYSQL DATABASE!"

    exit 1
fi
