# baldeweg/incwadi-core

incwadi is a book database to manage a lot of books.

## How it was made

An article can be found here https://medium.com/@A.Baldeweg/i-was-trying-new-things-accf33792e86

## Future

The first goal is to make a minimum viable product (MVP). It delivers only the features actually needed to be usable. As soon as this is done version 1.0.0 will be released. After that I will start developing new features. The development will follow the principles of lean development. Building small features, try it and decide weather it makes sense to invest more time into it.

I hope people will help in testing out the app as users or getting involved into development. Since the languages (PHP, JavaScript) and the used tools are widespread, it should be easy to getting onboard.

## Getting Started

### Requirements

- PHP 7.2
- MySQL
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
composer install
```

Now, create the database.

```shell
bin/console doctrine:database:create
bin/console doctrine:schema:update --force
```

Create your first user

```shell
bin/console user:new [NAME] ROLE_ADMIN [PASSWORD]
```
Replace NAME and PASSWORD with your desired data.

In case you are in dev, load the user with the fixtures instead.

```shell
bin/console doctrine:fixtures:load
```

## Update

Pull for the new files, update dependencies with Composer and update the database.

```shell
git pull
composer install
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
