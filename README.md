# incwadi/core

incwadi is a book database to manage a lot of books.

## How it was made

An article can be found here <https://medium.com/@A.Baldeweg/i-was-trying-new-things-accf33792e86>

## Future

The first objective is to make a minimum viable product (MVP). It delivers only the features actually needed to be usable. As soon as this is done version 1.0.0 will be released. After that I will start developing new features. The development will follow the principles of lean development. Building small features, try it and decide wether it makes sense to invest more time into it.

## Requirements

- PHP 7.2
- MySQL
- PHP Composer
- SSH access

## Getting Started

First, install PHP Composer, globally:

<https://getcomposer.org/download/>

Clone the repository:

```shell
git clone https://gitlab.com/incwadi/core.git
```

Point the web root to the `public/` dir.

Define env vars in your vHost. You need to set APP_ENV (set to "prod"), APP_SECRET, CORS_ALLOW_ORIGIN and DATABASE_URL. Refer to the section "Options" for more details.

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

## Dev

Clone the repository:

```shell
git clone https://gitlab.com/incwadi/core.git
```

Create the file `.env.local` and `.env.test.local`.

```shell
touch .env.local
touch .env.test.local
```

The content of both files could be the following. Please fit it to your needs and replace at least `DB_USER`, `DB_PASSWORD` and `DB_NAME`.

```shell
DATABASE_URL=mysql://DB_USER:DB_PASSWORD@127.0.0.1:3306/DB_NAME
```

Then install the composer dependencies and create the database.

```shell
composer install
bin/console doctrine:database:create
bin/console doctrine:schema:update --force
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

Example for Apache2:

```apache
SetEnv APP_ENV "prod"
SetEnv APP_SECRET "YOURSECRET"
SetEnv CORS_ALLOW_ORIGIN "^https?://localhost:?[0-9]*$"
SetEnv DATABASE_URL mysql://DB_USER:DB_PASSWORD@127.0.0.1:3306/DB_NAME
```

## CLI

- bin/console - Symfony commands
- bin/watch - Starts the development environment
- bin/stop - Stops the development environment
- bin/phpunit - Runs the PHPUnit tests
- bin/build - Runs the PHPUnit coverage report, generates stats and checks for code standard violations and fixes them partially

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
