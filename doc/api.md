# API

## Find books

`GET /v1/book/find`

Params

- criteria (required) - array
- orderBy - string
- limit - int
- offset - int

## Create book

`POST /v1/book/new`

Body

- title (required) - string
- author (required) - string
- genre (required) - int
- price (required) - decimal
- stocked (required) - bool

## Show book

`GET /v1/book/[id]`

Params

- id - int

## Edit book

`PUT /v1/book/[id]`

Params

- id - int

Body

- title (required) - string
- author (required) - string
- genre (required) - int
- price (required) - decimal
- stocked (required) - bool

## Sell a book

`PUT /v1/book/sell/[id]`

Params

- id - int

## Remove a book

`PUT /v1/book/remove/[id]`

Params

- id - int

## Delete book

`DELETE /v1/book/[id]`

Params

- id - int

## Show stats

`GET /v1/stats`

## List genres

`GET /v1/genre/`

## Create genre

`POST /v1/genre/new`

Body

- name - string

## Show genre

`GET /v1/genre/[id]`

Params

id - int

## Edit genre

`PUT /v1/genre/[id]`

Params

- id - int

Body

- name - string

## Delete genre

`DELETE /v1/genre/[id]`

Params

- id - int

## List branches

`GET /v1/branch/`

## My branch

`GET /v1/branch/my`

## Show branch

`GET /v1/branch/[id]`

Params

id - int

## Edit branch

`PUT /v1/branch/[id]`

Params

- id - int

Body

- name - string
- steps - float

## List staff members

`GET /v1/staff/`

## Create staff

`POST /v1/staff/new`

Body

- name - string
- notes - text

## Show staff

`GET /v1/staff/[id]`

Params

id - int

## Edit staff

`PUT /v1/staff/[id]`

Params

- id - int

Body

- name - string
- notes - text

## Delete staff

`DELETE /v1/staff/[id]`

Params

- id - int

## Find authors

`GET /v1/author/find`

Params:

- term - string

## Create author

`POST /v1/author/new`

Body

- name - string

## Edit author

`PUT /v1/author/[id]`

Params

- id - int

Body

- name - string

## Show author

`GET /v1/author/[id]`

Params

id - int

## Delete author

`DELETE /v1/author/[id]`

Params

- id - int

## Me

`GET /v1/me`

## Change Password

`PUT /v1/password`

Body

- password - string

## List conditions

`GET /v1/condition/`

## Create condition

`POST /v1/condition/new`

Body

- name - string

## Show condition

`GET /v1/condition/[id]`

Params

id - int

## Edit condition

`PUT /v1/condition/[id]`

Params

- id - int

Body

- name - string

## Delete condition

`DELETE /v1/condition/[id]`

Params

- id - int

## List report

`GET /v1/report/`

## Create report

`POST /v1/report/new`

Body

- name - string
- searchTerm - string
- limitTo - int
- sold - bool
- removed - bool
- olderThenXMonths - int
- branches - string
- genres - string
- lendMoreThenXMonths - int
- orderBy - string
- releaseYear - int
- type - string

## Show report

`GET /v1/report/[id]`

Params

id - int

## Edit report

`PUT /v1/report/[id]`

Params

- id - int

Body

- name - string
- searchTerm - string
- limitTo - int
- sold - bool
- removed - bool
- olderThenXMonths - int
- branches - string
- genres - string
- lendMoreThenXMonths - int
- orderBy - string
- releaseYear - int
- type - string

## Delete report

`DELETE /v1/report/[id]`

Params

- id - int

## List tags

`GET /v1/tag/`

## Create tag

`POST /v1/tag/new`

Body

- name - string

## Show tag

`GET /v1/tag/[id]`

Params

id - int

## Edit tag

`PUT /v1/tag/[id]`

Params

- id - int

Body

- name - string

## Delete tag

`DELETE /v1/tag/[id]`

Params

- id - int
