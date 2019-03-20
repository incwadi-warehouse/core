# baldeweg/incwadi-core

incwadi is a book database to manage a lot of books.

## How it was made

An article can be found here https://medium.com/@A.Baldeweg/i-was-trying-new-things-accf33792e86

## Future

The first goal is to make a minimum viable product (MVP). It delivers only the features actually needed to be usable. As soon as this is done version 1.0.0 will be released. After that I will start developing new features. The development will follow the principles of lean development. Building small features, try it and decide wether it makes sense to invest more time into it.

## Requirements

- PHP 7.2
- MySQL
- PHP Composer
- SSH access

## Getting Started

Clone the repository:

```shell
git clone https://gitlab.com/a.baldeweg/incwadi_core.git
```

Point the web root to the public/ dir.

Define env vars in your vHost. You need to set APP_ENV (set to "prod"), APP_SECRET, CORS_ALLOW_ORIGIN and DATABASE_URL. Refer to the section Options for more details.

Then install the composer dependencies.

```shell
composer install
```

Now, create the database.

```shell
bin/console doctrine:database:create
bin/console doctrine:schema:update --force
```

Create your first user

```shell
bin/console user:new [NAME] ROLE_ADMIN
```

Replace NAME and PASSWORD with your desired data.

For more details on how to deal with users, read the section Users Management.

## Dev

Clone the repository:

```shell
git clone https://gitlab.com/a.baldeweg/incwadi_core.git
```

Create the file .env.local and .env.test.local.

```shell
touch .env.local
touch .env.test.local
```

The content of both files could be the following. Please fit it to your needs.

```shell
DATABASE_URL=mysql://db_user:db_password@127.0.0.1:3306/db_name
```

Then install the composer dependencies.

```shell
composer install
```

Now, create the database.

```shell
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

or

```shell
git pull
composer install
bin/console doctrine:schema:update --force
```

## Options

Explanations for the env vars.

- APP_ENV - The environment, should be prod, dev or test.
- APP_SECRET - Contains a random string, more info at https://symfony.com/doc/current/reference/configuration/framework.html#secret
- CORS_ALLOW_ORIGIN - Contains a regex including the URI to the backend.
- DATABASE_URL - Credentials for the database.

Example for Apache2:

```apache
SetEnv APP_ENV "prod"
SetEnv APP_SECRET "YOURSECRET"
SetEnv CORS_ALLOW_ORIGIN "^https?://localhost:?[0-9]*$"
SetEnv DATABASE_URL mysql://db_user:db_password@127.0.0.1:3306/db_name
```

## CLI

- bin/console - Symfony commands
- bin/watch - Starts the development environment
- bin/stop - Stops the development environment
- bin/phpunit - Runs the PHPUnit tests
- bin/report - Runs the PHPUnit coverage report
- bin/lint - Checks for code standard violations and fixes them partially

## Users Management

Fetching a list with all users and their corresponding ID:

```shell
bin/console user:list
```

Create a new user with the role ROLE_USER. Replace [NAME] with the desired name of the user.

```shell
bin/console user:new [NAME]
```

If you want to create an admin user pass the param ROLE_ADMIN.

```shell
bin/console user:new [NAME] ROLE_ADMIN
```

You can of course delete a user. Replace [ID] with the ID of the user.

```shell
bin/console user:delete [ID]
```

If the user has forgotten the password, you can reset it with this command. Replace [ID] with the ID of the user.

```shell
bin/console user:reset-password [ID]
```

## API

### Find books

`GET /v1/book/find`

Params

- criteria (required) - array
- orderBy - string
- limit - int
- offset - int

### Create book

`POST /v1/book/new`

Body

- title (required) - string
- author (required) - string
- genre (required) - int
- price (required) - decimal
- stocked (required) - bool

### Show book

`GET /v1/book/[id]`

Params

- id - int

### Edit book

`PUT /v1/book/[id]`

Params

- id - int

Body

- title (required) - string
- author (required) - string
- genre (required) - int
- price (required) - decimal
- stocked (required) - bool

### Delete book

`DELETE /v1/book/[id]`

Params

- id - int

### List genres

`GET /v1/genre/`

### Create genre

`POST /v1/genre/new`

Body

- name - string

### Show genre

`GET /v1/genre/[id]`

Params

id - int

### Edit genre

`PUT /v1/genre/[id]`

Params

- id - int

Body

- name - string

### Delete genre

`DELETE /v1/genre/[id]`

Params

- id - int

### List branches

`GET /v1/branch/`

### Create branch

`POST /v1/branch/new`

Body

- name - string

### Show branch

`GET /v1/branch/[id]`

Params

id - int

### Edit branch

`PUT /v1/branch/[id]`

Params

- id - int

Body

- name - string

### Delete branch

`DELETE /v1/branch/[id]`

Params

- id - int

### List customers

`GET /v1/customer/`

### Create customer

`POST /v1/customer/new`

Body

- name - string
- notes - text

### Show customer

`GET /v1/customer/[id]`

Params

id - int

### Edit customer

`PUT /v1/customer/[id]`

Params

- id - int

Body

- name - string
- notes - text

### Delete customer

`DELETE /v1/customer/[id]`

Params

- id - int

### List lendings

`GET /v1/lending/`

### Create lending

`POST /v1/lending/new`

Body

- name - string
- customer - customer id

### Show lending

`GET /v1/lending/[id]`

Params

id - int

### Delete lending

`DELETE /v1/lending/[id]`

Params

- id - int
