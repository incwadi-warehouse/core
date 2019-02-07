# baldeweg/incwadi-core

incwadi is a book database to manage a lot of books.

## Getting Started

### Requirements

- PHP 7.2
- PHP Composer
- SSH access

### Install

```shell
git clone https://gitlab.com/a.baldeweg/incwadi_core.git
```

Define env vars in your vHost. For which have a look at `.env`.

In your dev env you can do

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
composer install --no-dev
```

In dev env omit the `--no-dev` param.

Now, create the database.

```shell
bin/console doctrine:database:create
bin/console doctrine:schema:update --force
```

## Update

Pull for the new files, update dependencies with Composer and update the database.

```shell
git pull
composer install --no-dev
bin/console doctrine:schema:update --force
```

## CLI

- bin/console - Symfony commands
- bin/watch - Starts the development environment
- bin/stop - Stops the development environment
- bin/phpunit - Runs the PHPUnit tests
- bin/report - Runs the PHPUnit coverage report
- bin/lint - Checks for code standard violations and fixes them partially

## API

### Find books

`GET /v1/book/find`

Params

- term (required) - string
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
