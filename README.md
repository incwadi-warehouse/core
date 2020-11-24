# incwadi/core

incwadi is a book database to manage your books.

## How it was made

An article can be found here <https://medium.com/@A.Baldeweg/i-was-trying-new-things-accf33792e86>.

## Requirements

- PHP 7.4
- MySQL
- SSH Access

## Getting Started

First, install PHP Composer, globally:

<https://getcomposer.org/download/>

Clone the repository:

```shell
git clone https://gitlab.com/incwadi/core.git
```

Point the web root to the `public` dir.

Create the file `.env.local` with the following content. Please fit it to your needs.

```shell
APP_SECRET=SECRET
DATABASE_URL=mysql://DB_USER:DB_PASSWORD@localhost:3306/DB_NAME
JWT_PASSPHRASE=PASSPHRASE
CORS_ALLOW_ORIGIN=^https?://DOMAIN(:[0-9]+)?$
```

To authenticate your users, you need to generate the SSL keys.

```shell
openssl genrsa -out config/jwt/private.pem -aes256 4096
openssl rsa -pubout -in config/jwt/private.pem -out config/jwt/public.pem
```

You also need this header for apache.

```apache
SetEnvIf Authorization "(.*)" HTTP_AUTHORIZATION=$1
```

More info on that: <https://github.com/lexik/LexikJWTAuthenticationBundle/blob/master/Resources/doc/index.md#important-note-for-apache-users>

Then install the composer dependencies and create the database.

```shell
bin/setup
```

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

<https://symfony.com/download>

Clone the repository:

```shell
git clone https://gitlab.com/incwadi/core.git
```

Create the file `.env.local` with the following content. Please fit it to your needs.

```shell
DATABASE_URL=mysql://DB_USER:DB_PASSWORD@localhost:3306/DB_NAME
```

To authenticate your users, you need to generate the SSL keys.

```shell
openssl genrsa -out config/jwt/private.pem -aes256 4096
openssl rsa -pubout -in config/jwt/private.pem -out config/jwt/public.pem
```

Then install the composer dependencies and create the database.

```shell
composer install
bin/console doctrine:database:create
bin/console doctrine:migrations:migrate -n
```

Load the first user with the fixtures.

```shell
bin/console doctrine:fixtures:load -n
```

## Update

Just call the following command.

```shell
bin/update
```

## CLI

- bin/console - Symfony commands
- bin/phpunit - Runs the PHPUnit tests.
- bin/build - Runs the PHPUnit coverage report, generates stats and checks for code standard violations and fixes them partially.
- bin/setup - Updates the app and creates the database.

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
