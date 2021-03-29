# API

## Books

### Find books

Request

`GET /api/v1/book/find`

Params

- criteria (required) - array
- orderBy - string
- limit - int
- offset - int

Response

- array of books

### Clean books

Request

`POST /api/v1/book/clean`

Response

- msg - string

### Show book

`GET /api/v1/book/[id]`

Params

- id - int

Response

- id - int
- branch - Branch|null
- added - DateTime
- title - string
- shortDescription - string|null
- author - Author|null
- genre - Genre|null
- price - float
- sold - bool
- soldOn - DateTime|null
- removed - bool
- removedOn - DateTime|null
- releaseYear - int
- type - string
- lendTo - Staff|null
- lendOn - DateTime|null
- condition - Condition|null
- tags - array\<Tag\>

### Create book

Request

`POST /api/v1/book/new`

Body

- title (required) - string
- author (required) - string
- genre (required) - int
- price (required) - decimal
- stocked (required) - bool

Response

- msg - string

or

- id - int
- branch - Branch|null
- added - DateTime
- title - string
- author - Author|null
- genre - Genre|null
- price - float
- sold - bool
- soldOn - DateTime|null
- removed - bool
- removedOn - DateTime|null
- releaseYear - int
- type - string
- lendTo - Staff|null
- lendOn - DateTime|null
- condition - Condition|null
- tags - array\<Tag\>

### Edit book

Request

`PUT /api/v1/book/[id]`

Params

- id - int

Body

- title (required) - string
- author (required) - string
- genre (required) - int
- price (required) - decimal
- stocked (required) - bool

Response

- msg - string

or

- id - int
- branch - Branch|null
- added - DateTime
- title - string
- author - Author|null
- genre - Genre|null
- price - float
- sold - bool
- soldOn - DateTime|null
- removed - bool
- removedOn - DateTime|null
- releaseYear - int
- type - string
- lendTo - Staff|null
- lendOn - DateTime|null
- condition - Condition|null
- tags - array\<Tag\>

### Show Cover

Request

`GET /api/v1/book/cover/[id]`

Params

- id - int

### Upload Cover

Request

`POST /api/v1/book/cover/[id]`

Params

- id - int

Body

- cover - binary

### Remove Cover

Request

`DELETE /api/v1/book/cover/[id]`

Params

- id - int

### Sell a book

Request

`PUT /api/v1/book/sell/[id]`

Params

- id - int

Response

- id - int
- branch - Branch|null
- added - DateTime
- title - string
- author - Author|null
- genre - Genre|null
- price - float
- sold - bool
- soldOn - DateTime|null
- removed - bool
- removedOn - DateTime|null
- releaseYear - int
- type - string
- lendTo - Staff|null
- lendOn - DateTime|null
- condition - Condition|null
- tags - array\<Tag\>

### Remove a book

Request

`PUT /api/v1/book/remove/[id]`

Params

- id - int

Response

- id - int
- branch - Branch|null
- added - DateTime
- title - string
- author - Author|null
- genre - Genre|null
- price - float
- sold - bool
- soldOn - DateTime|null
- removed - bool
- removedOn - DateTime|null
- releaseYear - int
- type - string
- lendTo - Staff|null
- lendOn - DateTime|null
- condition - Condition|null
- tags - array\<Tag\>

### Reserve a book

Request

`PUT /api/v1/book/reserve/[id]`

Params

- id - int

Response

- id - int
- branch - Branch|null
- added - DateTime
- title - string
- author - Author|null
- genre - Genre|null
- price - float
- sold - bool
- soldOn - DateTime|null
- removed - bool
- removedOn - DateTime|null
- releaseYear - int
- type - string
- lendTo - Staff|null
- lendOn - DateTime|null
- condition - Condition|null
- tags - array\<Tag\>

### Delete book

Request

`DELETE /api/v1/book/[id]`

Params

- id - int

Response

- msg - string

## Stats

### Show stats

Request

`GET /api/v1/stats`

Response

- all - int
- available - int
- reserved - int
- sold - int
- removed - int

## Genres

### List genres

Request

`GET /api/v1/genre/`

Response

- object with genres

### Show genre

Request

`GET /api/v1/genre/[id]`

Params

id - int

Response

- id - int
- name - string
- branch - Branch
- books - int

### Create genre

Request

`POST /api/v1/genre/new`

Body

- name - string

Response

- msg - string

or

- id - int
- name - string
- branch - Branch
- books - int

### Edit genre

Request

`PUT /api/v1/genre/[id]`

Params

- id - int

Body

- name - string

Response

- msg - string

or

- id - int
- name - string
- branch - Branch
- books - int

### Delete genre

Request

`DELETE /api/v1/genre/[id]`

Params

- id - int

Response

- msg - string

## Branches

### List branches

Request

`GET /api/v1/branch/`

Response

- array with branches

### My branch

Request

`GET /api/v1/branch/my`

Response

- id - int
- name - string
- steps - float
- currency - string

### Show branch

Request

`GET /api/v1/branch/[id]`

Params

id - int

Response

- id - int
- name - string
- steps - float
- currency - string

### Edit branch

Request

`PUT /api/v1/branch/[id]`

Params

- id - int

Body

- name - string
- steps - float
- currency - string

Response

- msg -string

or

- id - int
- name - string
- steps - float
- currency - string

## Staff Members

### List staff members

Request

`GET /api/v1/staff/`

Response

- array of staff members

### Show staff

Request

`GET /api/v1/staff/[id]`

Params

id - int

Response

- id - int
- name - string
- branch - Branch

### Create staff

Request

`POST /api/v1/staff/new`

Body

- name - string
- notes - text

Response

- msg -string

or

- id - int
- name - string
- branch - Branch

### Edit staff

Request

`PUT /api/v1/staff/[id]`

Params

- id - int

Body

- name - string
- notes - text

Response

- msg - string

or

- id - int
- name - string
- branch - Branch

### Delete staff

Request

`DELETE /api/v1/staff/[id]`

Params

- id - int

Response

- msg - string

## Authors

Request

### Find authors

`GET /api/v1/author/find`

Params:

- term - string

Response

- array of authors

### Show author

Request

`GET /api/v1/author/[id]`

Params

id - int

Response

- id - int
- firstname - string
- surname - string

### Create author

Request

`POST /api/v1/author/new`

Body

- name - string

Response

- msg - string

or

- id - int
- firstname - string
- surname - string

### Edit author

Request

`PUT /api/v1/author/[id]`

Params

- id - int

Body

- name - string

Response

- msg - string

or

- id - int
- firstname - string
- surname - string

### Delete author

Request

`DELETE /api/v1/author/[id]`

Params

- id - int

Response

- msg - string

## Profile

### Me

Request

`GET /api/v1/me`

Response

- id - int
- username - string
- roles - array
- branch - Branch
- isUser - bool
- isAdmin - bool

### Change Password

Request

`PUT /api/v1/password`

Body

- password - string

Response

- msg -string

## Condition

### List conditions

Request

`GET /api/v1/condition/`

Response

- array of conditions

### Create condition

Request

`POST /api/v1/condition/new`

Body

- name - string

Response

- id - int
- name - string
- branch - Branch

### Show condition

Request

`GET /api/v1/condition/[id]`

Params

id - int

Response

- id - int
- name - string
- branch - Branch

### Edit condition

Request

`PUT /api/v1/condition/[id]`

Params

- id - int

Body

- name - string

Response

- msg - string

or

- id - int
- name - string
- branch - Branch

### Delete condition

Request

`DELETE /api/v1/condition/[id]`

Params

- id - int

Response

- msg - string

## Tags

### List tags

Request

`GET /api/v1/tag/`

Response

- array of tags

### Show tag

Request

`GET /api/v1/tag/[id]`

Params

id - int

Response

- id - int
- name - string
- branch - Branch
- books - Book

### Create tag

Request

`POST /api/v1/tag/new`

Body

- name - string

Response

- msg - string

or

- id - int
- name - string
- branch - Branch
- books - Book

### Edit tag

Request

`PUT /api/v1/tag/[id]`

Params

- id - int

Body

- name - string

Response

- msg - string

or

- id - int
- name - string
- branch - Branch
- books - Book

### Delete tag

Request

`DELETE /api/v1/tag/[id]`

Params

- id - int

Response

- msg - string
## List saved searches

`GET /api/v1/savedsearch/`

## Create saved search

`POST /api/v1/savedsearch/new`

Body

- name - string
- query - array

## Show saved search

`GET /api/v1/savedsearch/[id]`

Params

id - int

## Edit saved search

`PUT /api/v1/savedsearch/[id]`

Params

- id - int

Body

- name - string
- query - array

## Delete saved search

`DELETE /api/v1/savedsearch/[id]`

Params

- id - int
