# API

## Find books

`GET /api/v1/book/find`

Params

- criteria (required) - array
- orderBy - string
- limit - int
- offset - int

## Create book

`POST /api/v1/book/new`

Body

- title (required) - string
- author (required) - string
- genre (required) - int
- price (required) - decimal
- stocked (required) - bool

## Show book

`GET /api/v1/book/[id]`

Params

- id - int

## Edit book

`PUT /api/v1/book/[id]`

Params

- id - int

Body

- title (required) - string
- author (required) - string
- genre (required) - int
- price (required) - decimal
- stocked (required) - bool

## Sell a book

`PUT /api/v1/book/sell/[id]`

Params

- id - int

## Remove a book

`PUT /api/v1/book/remove/[id]`

Params

- id - int

## Delete book

`DELETE /api/v1/book/[id]`

Params

- id - int

## Show stats

`GET /api/v1/stats`

## List genres

`GET /api/v1/genre/`

## Create genre

`POST /api/v1/genre/new`

Body

- name - string

## Show genre

`GET /api/v1/genre/[id]`

Params

id - int

## Edit genre

`PUT /api/v1/genre/[id]`

Params

- id - int

Body

- name - string

## Delete genre

`DELETE /api/v1/genre/[id]`

Params

- id - int

## List branches

`GET /api/v1/branch/`

## My branch

`GET /api/v1/branch/my`

## Show branch

`GET /api/v1/branch/[id]`

Params

id - int

## Edit branch

`PUT /api/v1/branch/[id]`

Params

- id - int

Body

- name - string
- steps - float
- currency - string

## List staff members

`GET /api/v1/staff/`

## Create staff

`POST /api/v1/staff/new`

Body

- name - string
- notes - text

## Show staff

`GET /api/v1/staff/[id]`

Params

id - int

## Edit staff

`PUT /api/v1/staff/[id]`

Params

- id - int

Body

- name - string
- notes - text

## Delete staff

`DELETE /api/v1/staff/[id]`

Params

- id - int

## Find authors

`GET /api/v1/author/find`

Params:

- term - string

## Create author

`POST /api/v1/author/new`

Body

- name - string

## Edit author

`PUT /api/v1/author/[id]`

Params

- id - int

Body

- name - string

## Show author

`GET /api/v1/author/[id]`

Params

id - int

## Delete author

`DELETE /api/v1/author/[id]`

Params

- id - int

## Me

`GET /api/v1/me`

## Change Password

`PUT /api/v1/password`

Body

- password - string

## List conditions

`GET /api/v1/condition/`

## Create condition

`POST /api/v1/condition/new`

Body

- name - string

## Show condition

`GET /api/v1/condition/[id]`

Params

id - int

## Edit condition

`PUT /api/v1/condition/[id]`

Params

- id - int

Body

- name - string

## Delete condition

`DELETE /api/v1/condition/[id]`

Params

- id - int

## List report

`GET /api/v1/report/`

## Create report

`POST /api/v1/report/new`

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

`GET /api/v1/report/[id]`

Params

id - int

## Edit report

`PUT /api/v1/report/[id]`

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

`DELETE /api/v1/report/[id]`

Params

- id - int

## List tags

`GET /api/v1/tag/`

## Create tag

`POST /api/v1/tag/new`

Body

- name - string

## Show tag

`GET /api/v1/tag/[id]`

Params

id - int

## Edit tag

`PUT /api/v1/tag/[id]`

Params

- id - int

Body

- name - string

## Delete tag

`DELETE /api/v1/tag/[id]`

Params

- id - int
