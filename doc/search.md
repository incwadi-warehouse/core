# Search

## Query

- term - string , Operator: like
- filters - array\<filter\>
- orderBy - array
  - book - array\<order\>
  - author - array\<order\>
- limit - integer

## Filter

- genre - array\<integer\>, Operator: in
- lendOn - integer, Operators: eq, gte, gt, lte, lt
- branches - integer, Operator: eq
- releaseYear - integer, Operator: eq, gte, gt, lte, lt
- sold - bool, Operator: eq
- removed - bool, Operator: eq
- type - string, Operator: eq
- added - integer, Operator: eq, gte, gt, lte, lt

## Order

- field - string
- direction - string
