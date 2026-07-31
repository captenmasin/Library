# Interactions and activity

Every endpoint on this page requires a valid bearer token and a verified email address.

## Pagination

Notes, reviews, and activities are returned newest first in Laravel's standard paginated resource shape. They accept `?page={number}` and use 10 items per page. The page size is not configurable.

## Notes

Notes are private to the current user.

### List notes

```http
GET /api/user/notes?page=1
```

Returns `200 OK` with a paginated collection. Each item has this shape:

```json
{
  "id": 15,
  "content": "Compare this chapter with the earlier argument.",
  "status": "Reading",
  "created_at": "2026-07-31T10:00:00.000000Z",
  "book": {}
}
```

The embedded `book` is a stored book resource with the relationships loaded by the notes list.

`status` is a snapshot of the book's reading status when the note was created; it does not change when the library status later changes.

### Create a note

```http
POST /api/books/{identifier}/notes
```

```json
{
  "content": "Compare this chapter with the earlier argument."
}
```

`content` is required, must be a string, and may contain at most 2,000 characters. The book must be in the user's library and the subscription must allow private notes.

Returns `201 Created` with the new note resource. The create response omits the conditional `book` field; use the identifier already present in the request. A plan restriction or missing library membership returns `403` with a `message`.

Only one note can currently exist for each user/book pair. The API exposes create and delete, but no note-update route; clients should not issue a second create request for the same pair.

### Delete a note

```http
DELETE /api/books/{identifier}/notes/{note}
```

`{note}` is the numeric note ID. The note must belong to the current user and the book in the path. A successful request returns `204 No Content`.

## Reviews

### List reviews

```http
GET /api/user/reviews?page=1
```

Returns `200 OK` with a paginated collection. Each review has this shape:

```json
{
  "id": 21,
  "uuid": "0198bce8-5fdd-7000-8000-000000000000",
  "title": "Worth reading",
  "content": "A concise review.",
  "created_at": "2026-07-31T10:00:00.000000Z",
  "updated_at": "2026-07-31T10:00:00.000000Z",
  "user": {},
  "book": {},
  "rating": {"id": 9, "value": 5}
}
```

`rating` is the current user's rating for the same book, or `null`.

### Create or update a review

```http
POST /api/books/{identifier}/reviews
```

This is an upsert: the first request creates the user's review and later requests replace its title and content.

```json
{
  "title": "Worth reading",
  "content": "A concise review."
}
```

Both fields may be `null`; `title` has a maximum of 256 characters and `content` has a maximum of 2,000. Clients should include both keys when saving.

- A new review returns `201 Created` with the review resource.
- An updated review returns `200 OK` with the review resource.

A verified user may review any stored catalog book; the book does not need to be in their library.

The create/update response loads `user` and `rating` but omits the conditional `book` field. The paginated review list includes all three.

### Delete a review

```http
DELETE /api/books/{identifier}/reviews/{review}
```

`{review}` is the review UUID, not its numeric ID. The review must belong to the current user and the book in the path. A successful request returns `204 No Content`.

## Ratings

Ratings use integer values from 1 through 5. The book must be in the current user's library.

### Create a rating

```http
POST /api/books/{identifier}/ratings
```

```json
{
  "rating": {
    "value": 4
  }
}
```

Returns `201 Created`:

```json
{
  "id": 9,
  "value": 4
}
```

Only one rating can exist for each user/book pair. After creation, use the returned rating ID with the update route rather than posting another rating.

### Update a rating

```http
PUT /api/books/{identifier}/ratings/{rating}
```

`{rating}` is the numeric rating ID.

```json
{
  "rating": {
    "value": 5
  }
}
```

Returns `200 OK` with the updated rating resource.

The rating must belong to the current user, match the book in the path, and the book must remain in that user's library. Another user's rating returns `403`; a rating scoped to another book returns `404`.

### Delete a rating

```http
DELETE /api/books/{identifier}/ratings/{rating}
```

The rating must belong to the current user and the book in the path. A successful request returns `204 No Content`.

Rating mutations do not create entries in the activity feed.

## Custom covers

The book must be in the current user's library. Uploading additionally requires a subscription that allows custom covers; deletion remains available after a downgrade.

### Upload a cover

```http
POST /api/books/{identifier}/cover
```

Send `multipart/form-data` with a `cover` file:

```bash
curl -X POST https://bookbound.test/api/books/9780441172719/cover \
  -H 'Accept: application/json' \
  -H 'Authorization: Bearer 1|long-sanctum-token' \
  -F 'cover=@/path/to/cover.jpg'
```

Accepted extensions are `jpg`, `jpeg`, `png`, `webp`, and `gif`; the maximum size is 20 MiB. A successful upload returns `201 Created` with the stored book resource. A subscription restriction returns `403` with a `message`.

Although the backend accepts an omitted file as a no-op and returns `200`, mobile clients should always include `cover` on this route.

### Delete a cover

```http
DELETE /api/books/{identifier}/cover
```

Deletes custom covers belonging to the current user for this book and returns `204 No Content`.

## Activity

```http
GET /api/user/activities?page=1
```

Returns `200 OK` with a paginated collection. Each item has this shape:

```json
{
  "id": 81,
  "type": "book.status.updated",
  "description": "...",
  "properties": {
    "book_identifier": "9780441172719",
    "book_title": "Dune",
    "status": "Read"
  },
  "created_at": "2026-07-31T10:00:00.000000Z"
}
```

`description` may contain HTML such as `<strong>`, `<em>`, and `&mdash;`. Render it with an HTML-aware component that sanitizes input, or strip the markup before displaying plain text.

`properties` is normally an object, but an activity with no properties may serialize it as an empty array. Mobile decoders should accept either shape for the empty value.

Current activity types are:

```text
book.added
book.status.updated
book.removed
book.note.added
book.note.updated
book.note.removed
book.review.added
book.review.updated
book.review.removed
book.cover.updated
book.cover.removed
```
