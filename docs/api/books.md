# Books and library

Every endpoint on this page requires a valid bearer token and a verified email address.

## Reading statuses

The API uses these exact, case-sensitive values:

```text
Plan to Read
Reading
Read
On Hold
Dropped
```

## Dashboard

```http
GET /api/dashboard
```

Returns `200 OK` with summary counts, up to four currently-reading books, five recent activities, ten common global tags, and five most-read authors.

```json
{
  "stats": {
    "books_in_library": 12,
    "completed_books": 5,
    "reading_books": 2,
    "plan_to_read": 4
  },
  "currently_reading": [],
  "activities": [],
  "tags": [],
  "authors": []
}
```

## Search books

```http
GET /api/books/search
```

Searches the configured external book provider.

| Query parameter | Type | Rules and default |
| --- | --- | --- |
| `q` | string | Optional search text, maximum 255 characters. |
| `author` | string | Optional author search, maximum 255 characters. |
| `subject` | string | Optional subject search, maximum 255 characters. |
| `page` | integer | Optional, minimum 1, default 1. |
| `per_page` | integer | Optional, 1–30, default 30. |

```http
GET /api/books/search?q=dune&author=frank+herbert&page=1&per_page=20
```

### Success: `200 OK`

```json
{
  "total": 125,
  "books": [
    {
      "id": "provider-id",
      "identifier": "9780441172719",
      "codes": [
        {"type": "ISBN_13", "identifier": "9780441172719"},
        {"type": "ISBN_10", "identifier": "0441172717"}
      ],
      "title": "Dune",
      "pageCount": 412,
      "tags": ["Science Fiction"],
      "publisher": {"name": "Ace", "uuid": null},
      "description": "...",
      "description_clean": "...",
      "authors": [{"name": "Frank Herbert", "uuid": null}],
      "edition": null,
      "binding": "Paperback",
      "type": "physical",
      "language": "eng",
      "published_date": "1965",
      "cover": "https://example.test/cover.jpg",
      "cover_large": "https://example.test/cover-large.jpg",
      "service": "provider-name",
      "links": {"show": "https://bookbound.test/books/..."}
    }
  ]
}
```

Search books are provider-transformed records, not stored `BookResource` objects. In particular, search uses `pageCount` while stored books use `page_count`. Search also dispatches background import jobs for returned books.

## Resolve or import a book

```http
POST /api/books/resolve
```

Looks up a stored book by identifier or imports it from the configured provider.

```json
{
  "identifier": "9780441172719"
}
```

`identifier` is required, must be a string, and has a maximum length of 255 characters.

### Success: `200 OK`

```json
{
  "book": {
    "id": 42,
    "identifier": "9780441172719",
    "title": "Dune"
  }
}
```

An upstream provider or import failure is currently returned as a server error rather than a defined `404`.

For an existing book, `authors`, `publisher`, and `tags` are loaded in this response. A freshly imported book is returned before those relationships are reloaded, so those conditional keys may be omitted on the first resolve and appear on a later request.

### Compatibility route

```http
GET /api/books/fetch/{identifier}
```

This returns the same response and can also import a missing book. New clients should prefer `POST /api/books/resolve` because it represents the write side effect correctly.

## Book details

```http
GET /api/books/{identifier}
```

Returns a stored book by its identifier.

### Success: `200 OK`

```json
{
  "book": {
    "id": 42,
    "path": "dune-9780441172719",
    "identifier": "9780441172719",
    "title": "Dune",
    "description": "...",
    "description_clean": "...",
    "published_date": "1965-08-01",
    "tags": [],
    "page_count": 412,
    "has_custom_cover": false,
    "cover": "https://example.test/cover.jpg",
    "authors": [],
    "publisher": null,
    "user_notes": [],
    "user_review": null,
    "user_rating": {"id": 9, "value": 5},
    "average_rating": 4.5,
    "ratings_count": 20,
    "in_library": true,
    "user_status": "Reading",
    "user_tags": ["favourite"],
    "edition": null,
    "binding": "Paperback",
    "type": "physical",
    "language": "eng",
    "colour": "#b38b59",
    "created_at": "2026-07-31T10:00:00.000000Z",
    "updated_at": "2026-07-31T10:00:00.000000Z",
    "links": {"show": "https://bookbound.test/books/..."}
  },
  "average_rating": "4.5",
  "related": [],
  "reviews": []
}
```

The top-level `average_rating` is a one-decimal string. `related` contains up to four stored book resources. `reviews` excludes the current user's review because it is available as `book.user_review`.

### Stored book fields

Relationship-dependent fields are omitted when the endpoint did not load that relationship.

| Field | Type | Description |
| --- | --- | --- |
| `id` | integer | Internal book ID. |
| `path` | string | Web route slug. |
| `identifier` | string | Primary identifier, normally ISBN. |
| `title` | string | Decoded title. |
| `description` | string | Original description. |
| `description_clean` | string | HTML-free description. |
| `published_date` | string or null | Publication date. |
| `tags` | object[] | Global imported tags, when loaded. |
| `page_count` | integer or null | Page count. |
| `has_custom_cover` | boolean | Whether this user has an active custom cover. |
| `cover` | string | User-specific or primary cover URL. |
| `authors` | object[] | Authors, when loaded. |
| `publisher` | object or null | Publisher, when loaded. |
| `user_notes` | object[] | Current user's notes, when loaded. |
| `user_review` | object or null | Current user's review, when loaded. |
| `user_rating` | object or null | Current user's rating, when loaded. |
| `average_rating` | number or null | Average of loaded ratings. |
| `ratings_count` | integer | Count of loaded ratings. |
| `in_library` | boolean | Whether the current user has this book. |
| `user_status` | string or null | Current user's reading status. |
| `user_tags` | string[] or null | Private labels stored on the library entry, or `null` when it is not in the library. |
| `edition` | string or null | Edition text. |
| `binding` | string | Normalized binding. |
| `type` | string | `physical`, `digital`, or `audio`. |
| `language` | string or null | Language code or label. |
| `colour` | string | Book colour setting. |
| `created_at` | string | ISO-8601 timestamp. |
| `updated_at` | string | ISO-8601 timestamp. |
| `links.show` | string | Web book URL. |

## List the library

```http
GET /api/user/books
```

This endpoint returns all matching books and is not paginated.

| Query parameter | Values | Description |
| --- | --- | --- |
| `status` or `status[]` | Reading status values | Filter by one or more statuses. |
| `tag` | Global tag slug | Filter imported book tags. |
| `author` | Author slug | Filter by author. |
| `search` | string | Match title, description, author, or identifier. |
| `sort` | `title`, `rating`, `published_date`, `added`, `author`, `colour` | Sort field; defaults to date added. |
| `order` | `asc`, `desc` | Sort direction; defaults to `desc`. |

The endpoint does not reject invalid filter values. Invalid statuses are ignored, an invalid `sort` falls back to date-added ordering and is returned as `filters.sort: null`, and any `order` other than the exact value `desc` sorts ascending. When `sort` is omitted, date-added ordering is used but `filters.sort` is also `null`.

```json
{
  "books": [],
  "total": 12,
  "filtered_total": 3,
  "authors": [
    {"uuid": "...", "slug": "frank-herbert", "name": "Frank Herbert"}
  ],
  "tags": [
    {"id": 1, "slug": "science-fiction", "name": "Science Fiction"}
  ],
  "filters": {
    "statuses": ["Reading"],
    "author": null,
    "tag": null,
    "sort": "title",
    "order": "asc",
    "search": "dune"
  }
}
```

Per-book library resources load authors and ratings but omit conditional global `tags` and `publisher` fields. The top-level `authors` and `tags` facets are calculated from the whole library and are not narrowed by active filters.

`tag` filters global imported `book.tags`. The private string labels managed by `PUT /api/user/{identifier}/tags` are returned in each book's `user_tags` but do not affect this filter.

## Add a book to the library

```http
POST /api/user/books
```

The book must already be stored. Use resolve first when starting from a provider search result.

```json
{
  "identifier": "9780441172719",
  "status": "Plan to Read"
}
```

Both fields are required. `identifier` must exist in the `books` table and `status` must be one of the documented reading statuses.

### Success: `200 OK`

```json
{
  "success": true,
  "message": "Book added to your library successfully."
}
```

Adding a duplicate or exceeding the subscription's book limit returns `400` with `success: false` and a message.

## Update reading status

```http
PATCH /api/user/{identifier}/status
```

```json
{
  "status": "Read"
}
```

### Success: `200 OK`

```json
{
  "success": true,
  "message": "Book status updated successfully.",
  "status": "Read"
}
```

Clients should always send `status`. If it is omitted, the current implementation resets the stored status to `Plan to Read` but returns `status: null` in the immediate response.

## Replace private labels

```http
PUT /api/user/{identifier}/tags
```

```json
{
  "tags": ["favourite", "book-club"]
}
```

`tags` must be present, contain at most 20 distinct strings, and each string may contain at most 50 characters. Send an empty array to clear all labels.

### Success: `200 OK`

```json
{
  "message": "Tags updated successfully.",
  "tags": ["favourite", "book-club"]
}
```

The book must be in the current user's library; otherwise the endpoint returns `404`.

## Remove a book from the library

```http
DELETE /api/user/{identifier}
```

Returns `200 OK`:

```json
{
  "success": true,
  "message": "Book removed from your library successfully."
}
```

This removes the library entry and the current user's custom cover for that book.
