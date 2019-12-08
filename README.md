# incwadi/core

incwadi is a book database to manage your books.

## How it was made

An article can be found here <https://medium.com/@A.Baldeweg/i-was-trying-new-things-accf33792e86>.

## Requirements

- PHP 7.2
- MySQL
- PHP Composer
- SSH Access

## Getting Started

First, install PHP Composer, globally:

<https://getcomposer.org/download/>

Clone the repository:

```shell
git clone https://gitlab.com/incwadi/core.git
```

Point the web root to the `public/` dir.

Create the file `.env.local`.

```shell
touch .env.local
```

The content of the file could be the following. Please fit it to your needs. You find more about it in the section "Options".

```shell
APP_ENV=prod
APP_SECRET=SECRET
CORS_ALLOW_ORIGIN=^https?://DOMAIN:?[0-9]*$
DATABASE_URL=mysql://DB_USER:DB_PASSWORD@localhost:3306/incwadi
JWT_SECRET_KEY=%kernel.project_dir%/config/jwt/private.pem
JWT_PUBLIC_KEY=%kernel.project_dir%/config/jwt/public.pem
JWT_PASSPHRASE=PASSPHRASE
```

Then install the composer dependencies and create the database.

```shell
bin/setup
```

To authenticate your users, you need to generate an SSH key.

```shell
mkdir -p config/jwt/
openssl genrsa -out config/jwt/private.pem -aes256 4096
openssl rsa -pubout -in config/jwt/private.pem -out config/jwt/public.pem
```

Test whether the login works as expected.

```shell
curl -X POST -H "Content-Type: application/json" http://127.0.0.1:8000/api/login_check -d '{"username":"admin","password":"password"}'
```

You also need this header for apache.

```apache
SetEnvIf Authorization "(.*)" HTTP_AUTHORIZATION=$1
```

More info on that: <https://github.com/lexik/LexikJWTAuthenticationBundle/blob/master/Resources/doc/index.md#important-note-for-apache-users>

It's recommended to have at least one branch. Create it with the following command. Replace `[NAME]` with your desired name of the branch. For more about branches read the section "Branches".

```shell
bin/console branch:new [NAME]
```

Create your first user. Replace `[BRANCH]` with the id, that was returned in the previous command.

```shell
bin/console user:new [NAME] ROLE_ADMIN [BRANCH]
```

Replace `[NAME]` with your desired data.

For more details on how to deal with users, read the section "Users Management".

Add the following script to your Cron Jobs and let it run at least daily.

```shell
bin/console book:delete -q
```

## Dev

Install the Symfony binary.

https://symfony.com/download

Clone the repository:

```shell
git clone https://gitlab.com/incwadi/core.git
```

Create the files `.env.local` and `.env.test.local`.

```shell
touch .env.local
touch .env.test.local
```

The content of both files could be the following. Please fit it to your needs and replace at least `DB_USER` and `DB_PASSWORD`.

```shell
DATABASE_URL=mysql://DB_USER:DB_PASSWORD@127.0.0.1:3306/incwadi
JWT_PASSPHRASE=PASSPHRASE
```

To authenticate your users, you need to generate an SSH key.

```shell
mkdir -p config/jwt/
openssl genrsa -out config/jwt/private.pem -aes256 4096
openssl rsa -pubout -in config/jwt/private.pem -out config/jwt/public.pem
```

Then install the composer dependencies and create the database.

```shell
composer install
bin/console doctrine:database:create
bin/console doctrine:migrations:migrate
```

Load the first user with the fixtures.

```shell
bin/console doctrine:fixtures:load
```

## Update

Pull for the new files, update dependencies with Composer and update the database.

```shell
bin/update
```

## Options

Explanations for the env vars.

- APP_ENV - The environment, should be prod, dev or test.
- APP_SECRET - Contains a random string, more info at <https://symfony.com/doc/current/reference/configuration/framework.html#secret>
- CORS_ALLOW_ORIGIN - Contains a regex including the URI to the backend.
- DATABASE_URL - Credentials for the database.
- JWT_SECRET_KEY - Path to your secret key.
- JWT_PUBLIC_KEY - Path to your public key.
- JWT_PASSPHRASE - This is the passphrase that protects your key.

## CLI

- bin/console - Symfony commands
- bin/watch - Starts the development environment.
- bin/stop - Stops the development environment.
- bin/phpunit - Runs the PHPUnit tests.
- bin/build - Runs the PHPUnit coverage report, generates stats and checks for code standard violations and fixes them partially.
- bin/dump - Starts the Dump Server.
- bin/setup - Installs the app.
- bin/update - Starts the update process.
- bin/backup - Makes a dump of the database.

## Branches

Branches can only be created on the command line.

Find out what branches are existing and the corresponding id for a specific branch.

```shell
bin/console branch:list
```

Creating a new branch is straightforward. Replace `[NAME]` with your desired name.

```shell
bin/console branch:new [NAME]
```

## Users Management

Fetching a list with all users and their corresponding id:

```shell
bin/console user:list
```

Create a new user and replace `[NAME]` with the desired name of the user. Set `[ROLE]` that's either `ROLE_USER` or `ROLE_ADMIN`. The `[BRANCH]` is the id of the branch the user is supposed to be a part of.

```shell
bin/console user:new [NAME] [ROLE] [BRANCH]
```

You can of course delete a user. Replace `[ID]` with the id of the user.

```shell
bin/console user:delete [ID]
```

If the user has forgotten the password, you can reset it with this command. Replace `[ID]` with the id of the user.

```shell
bin/console user:reset-password [ID]
```

## Backup

The most important thing to update is the database. One example how you can achieve that. The following commands assumes you are in the home directory of root and you have installed the core into /var/www/core, please fit the commands to your needs. Also, you need to install rsync.

On the remote machine:

```shell
/var/www/core/bin/backup
```

On your local machine:

```shell
rsync -azvv -e ssh [USER]@[HOST]:/root/incwadi.sql ~/incwadi.sql
```