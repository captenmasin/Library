# Bookbound Mobile API

Bookbound exposes an unversioned JSON API for the workflows currently available in the application. The API uses Laravel Sanctum personal access tokens, so a native mobile client can authenticate without cookies or CSRF tokens.

## Base URL

All paths in this documentation are relative to:

```text
{APP_URL}/api
```

The default Laravel Herd URL in local development is:

```text
https://bookbound.test/api
```

## Quick start

Exchange an email address or username and password for a device token:

```bash
curl -X POST https://bookbound.test/api/login \
  -H 'Accept: application/json' \
  -H 'Content-Type: application/json' \
  -d '{
    "login": "reader@example.com",
    "password": "secret-password",
    "device_name": "Mason’s iPhone"
  }'
```

The response contains a plain-text token. This is the only time the full token is returned. The user object below is abridged; see [Authentication and account](authentication.md#current-user) for every field.

```json
{
  "token": "1|long-sanctum-token",
  "user": {
    "id": 1,
    "name": "Mason",
    "username": "mason",
    "email": "reader@example.com",
    "email_verified": true
  }
}
```

Send that token on subsequent requests:

```bash
curl https://bookbound.test/api/dashboard \
  -H 'Accept: application/json' \
  -H 'Authorization: Bearer 1|long-sanctum-token'
```

## Request headers

| Header | When required | Value |
| --- | --- | --- |
| `Accept` | Every request | `application/json` |
| `Authorization` | Every route except `POST /login` | `Bearer {token}` |
| `Content-Type` | JSON request bodies | `application/json` |
| `Content-Type` | Avatar and cover uploads | `multipart/form-data` |

Always send `Accept: application/json`. Some controllers are shared with the web application and use this header to select JSON responses instead of browser redirects.

## Access levels

| Access | Meaning |
| --- | --- |
| Public | No token is required. |
| Token | A valid Sanctum bearer token is required. |
| Verified | A valid bearer token and a verified email address are required. |

Changing the account email address marks it as unverified. The account endpoints remain available, but dashboard, book, library, note, review, rating, cover, and activity endpoints return `403` until verification is complete.

## Endpoint index

### Authentication and account

See [Authentication and account](authentication.md).

| Method | Path | Access | Purpose |
| --- | --- | --- | --- |
| `POST` | `/login` | Public | Create a device token from credentials. |
| `GET` | `/user` | Token | Get the current account. |
| `DELETE` | `/logout` | Token | Revoke the current device token. |
| `POST` | `/email/verification-notification` | Token | Resend the verification email. |
| `POST` | `/user/profile` | Token | Update profile fields or avatar. |
| `DELETE` | `/user/avatar` | Token | Remove the avatar. |
| `PUT` | `/user/password` | Token | Change the password. |
| `PATCH` | `/user/settings/single` | Token | Update one setting. |
| `PATCH` | `/user/settings/multiple` | Token | Update multiple settings. |
| `DELETE` | `/user` | Token | Delete the account and all tokens. |

### Books and library

See [Books and library](books.md).

| Method | Path | Access | Purpose |
| --- | --- | --- | --- |
| `GET` | `/dashboard` | Verified | Get the mobile dashboard. |
| `GET` | `/books/search` | Verified | Search the configured book provider. |
| `POST` | `/books/resolve` | Verified | Fetch or import a book by identifier. |
| `GET` | `/books/fetch/{identifier}` | Verified | Compatibility alias for resolve. |
| `GET` | `/books/{identifier}` | Verified | Get stored book details. |
| `GET` | `/user/books` | Verified | Get and filter the current library. |
| `POST` | `/user/books` | Verified | Add a stored book to the library. |
| `PATCH` | `/user/{identifier}/status` | Verified | Change reading status. |
| `PUT` | `/user/{identifier}/tags` | Verified | Replace private labels on a library entry. |
| `DELETE` | `/user/{identifier}` | Verified | Remove a book from the library. |

### Notes, reviews, ratings, covers, and activity

See [Interactions and activity](interactions.md).

| Method | Path | Access | Purpose |
| --- | --- | --- | --- |
| `GET` | `/user/notes` | Verified | List the current user's notes. |
| `POST` | `/books/{identifier}/notes` | Verified | Create a private note. |
| `DELETE` | `/books/{identifier}/notes/{note}` | Verified | Delete a private note. |
| `GET` | `/user/reviews` | Verified | List the current user's reviews. |
| `POST` | `/books/{identifier}/reviews` | Verified | Create or update a review. |
| `DELETE` | `/books/{identifier}/reviews/{review}` | Verified | Delete a review by UUID. |
| `POST` | `/books/{identifier}/ratings` | Verified | Create a rating. |
| `PUT` | `/books/{identifier}/ratings/{rating}` | Verified | Update a rating. |
| `DELETE` | `/books/{identifier}/ratings/{rating}` | Verified | Delete a rating. |
| `POST` | `/books/{identifier}/cover` | Verified | Upload a custom cover. |
| `DELETE` | `/books/{identifier}/cover` | Verified | Delete all of the current user's custom covers for the book. |
| `GET` | `/user/activities` | Verified | List recent account activity. |

## Response conventions

Single resources are returned directly, without a top-level `data` wrapper. Paginated collections use Laravel's standard structure:

```json
{
  "data": [],
  "links": {
    "first": "https://example.test/api/user/notes?page=1",
    "last": "https://example.test/api/user/notes?page=1",
    "prev": null,
    "next": null
  },
  "meta": {
    "current_page": 1,
    "from": null,
    "last_page": 1,
    "path": "https://example.test/api/user/notes",
    "per_page": 10,
    "to": null,
    "total": 0,
    "links": [
      {
        "url": null,
        "label": "&laquo; Previous",
        "page": null,
        "active": false
      },
      {
        "url": "https://example.test/api/user/notes?page=1",
        "label": "1",
        "page": 1,
        "active": true
      },
      {
        "url": null,
        "label": "Next &raquo;",
        "page": null,
        "active": false
      }
    ]
  }
}
```

Dates are serialized as ISO-8601 strings. A successful delete normally returns `204 No Content`; library removal is the exception and returns a JSON confirmation with status `200`.

## Errors

| Status | Meaning |
| --- | --- |
| `400` | A domain rule failed, such as adding the same book twice. |
| `401` | The bearer token is missing, invalid, or revoked. |
| `403` | Email verification, ownership, library membership, or subscription access is required. |
| `404` | A route-bound book or nested resource was not found. |
| `422` | Request validation or credential validation failed. |
| `429` | The API rate limit was exceeded. |
| `500` | An unexpected server or upstream book-provider failure occurred. |

Most validation errors use Laravel's standard shape:

```json
{
  "message": "The login field is required.",
  "errors": {
    "login": [
      "The login field is required."
    ]
  }
}
```

The settings endpoints use manual validators and return slightly different error envelopes; see [Authentication and account](authentication.md#update-one-setting).

## Rate limits

- All API routes are limited to 60 requests per minute.
- Verification email requests are additionally limited to 6 per minute.
- Failed logins are limited to 5 attempts per normalized login and IP address. The login lockout is returned as a `422` error on `login`; the general API limiter returns `429`.

## Route identifiers

| Placeholder | Value |
| --- | --- |
| `{identifier}` | A book's `identifier`, normally an ISBN. |
| `{note}` | Numeric note ID. |
| `{review}` | Review UUID. |
| `{rating}` | Numeric rating ID. |

Nested note, review, and rating routes are scoped to the book in the URL. A child identifier from another book returns `404`.

## Current scope

The mobile API authenticates existing accounts. Registration, forgotten-password flows, passkeys, verification completion, billing, and administration remain web-only and are not part of this API contract. The API can resend a verification email, but the signed link currently completes through the browser-authenticated web flow.
