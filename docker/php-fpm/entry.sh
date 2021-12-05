#!/bin/sh

setup()
{
  echo "SETUP"

  composer dump-env prod

  bin/console doctrine:database:create --if-not-exists
  bin/console doctrine:migrations:migrate -n
  bin/console cache:clear
  bin/console cache:warmup

  CONFIG=/usr/local/apache2/htdocs/config/jwt

  if [ ! -f ${CONFIG}/private.pem ] || [ ! -f ${CONFIG}/public.pem ]
    then
      bin/console lexik:jwt:generate-keypair
  fi

  chmod 0666 ${CONFIG}/private.pem
  chmod 0666 ${CONFIG}/public.pem
}

waitForServer()
{
  for i in $(seq 1 60)
    do
      nc -z db 3306 && setup && return 0
      echo .
      sleep 1
  done
  echo "CANNOT CONNECT TO MYSQL DATABASE!" && exit 1
}

waitForServer
