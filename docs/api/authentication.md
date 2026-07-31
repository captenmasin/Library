# Authentication and account

These endpoints use Sanctum personal access tokens. Only `POST /login` is public; every other endpoint on this page requires `Authorization: Bearer {token}`. Email verification is not required for account-management endpoints.

## Log in

```http
POST /api/login
```

### Request

| Field | Type | Rules |
| --- | --- | --- |
| `login` | string | Required. May be the user's email address or username. |
| `password` | string | Required. |
| `device_name` | string | Required, maximum 255 characters. Use a name the user can recognize. |

```json
{
  "login": "reader@example.com",
  "password": "secret-password",
  "device_name": "Pixel 10"
}
```

### Success: `200 OK`

```json
{
  "token": "1|long-sanctum-token",
  "user": {
    "id": 1,
    "name": "Alex Reader",
    "username": "alex",
    "avatar": "",
    "email_verified": true,
    "colour": "#f2ead9",
    "email": "reader@example.com",
    "settings": {
      "library": {
        "view": "grid",
        "tilt_books": true
      },
      "single_book": {
        "default_section": "notes"
      },
      "profile": {
        "colour": "#f2ead9"
      }
    },
    "permissions": [],
    "book_identifiers": [],
    "subscription": {
      "subscribed": false,
      "plan": "free",
      "limits": {
        "max_books": 50,
        "private_notes": false,
        "custom_covers": false
      },
      "books": {
        "count": 0,
        "max": 50,
        "remaining": 50
      },
      "can_add_book": true,
      "allow_private_notes": false,
      "allow_custom_covers": false
    }
  }
}
```

The subscription values above are illustrative; limits and feature access come from the user's current plan.

Store the token in the platform's secure credential storage. Treat it as an opaque secret and replace it with a new login token after any `401` response.

Tokens currently have no configured expiry and use Sanctum's wildcard ability. They can still be revoked by logout, account deletion, or server-side administration, so the client must always handle `401`.

### Credential errors: `422 Unprocessable Content`

Invalid credentials are deliberately reported against `login` without revealing whether the account exists.

```json
{
  "message": "These credentials do not match our records.",
  "errors": {
    "login": [
      "These credentials do not match our records."
    ]
  }
}
```

## Current user

```http
GET /api/user
```

Returns the same user object included in the login response.

### User fields

| Field | Type | Description |
| --- | --- | --- |
| `id` | integer | Account ID. |
| `name` | string | Display name. |
| `username` | string or null | Unique username, when configured. |
| `avatar` | string | Avatar URL, or an empty string when none is stored. |
| `email_verified` | boolean | Whether verified-only routes are available. |
| `colour` | string | Profile colour as a hex value. |
| `email` | string | Current email address. |
| `settings` | object | Model-backed account settings. |
| `permissions` | string[] | Effective permission names. |
| `book_identifiers` | object or [] | Map of book identifier to reading status; an empty library serializes as `[]`. |
| `subscription` | object | Current plan, limits, usage, and feature flags. |

## Log out this device

```http
DELETE /api/logout
```

Returns `204 No Content` and revokes only the bearer token used for the request. Tokens issued to other devices remain valid.

## Resend email verification

```http
POST /api/email/verification-notification
```

No body is required.

### Success: `200 OK`

For an unverified account:

```json
{
  "message": "Verification link sent."
}
```

For an already verified account:

```json
{
  "message": "Email already verified."
}
```

This endpoint is limited to 6 requests per minute.

This endpoint only sends the message. The signed link currently completes verification through the existing browser-authenticated web route; there is no API verification-completion endpoint.

## Update profile

```http
POST /api/user/profile
```

Send JSON when updating text fields only. Use `multipart/form-data` when including `avatar`. The three core profile fields are required on every request.

| Field | Type | Rules |
| --- | --- | --- |
| `name` | string | Required, maximum 255 characters. |
| `username` | string | Required, letters/numbers/dashes/underscores, maximum 255, unique. |
| `email` | string | Required, lowercase valid email, maximum 255, unique. |
| `avatar` | image | Optional, maximum 8 MiB. |
| `profile_colour` | string | Optional `#RRGGBB` value. |

```bash
curl -X POST https://bookbound.test/api/user/profile \
  -H 'Accept: application/json' \
  -H 'Authorization: Bearer 1|long-sanctum-token' \
  -F 'name=Alex Reader' \
  -F 'username=alex' \
  -F 'email=reader@example.com' \
  -F 'profile_colour=#334455' \
  -F 'avatar=@/path/to/avatar.jpg'
```

Returns `200 OK` with the complete user object. Changing `email` clears verification and sends a new verification message.

## Delete avatar

```http
DELETE /api/user/avatar
```

Returns `204 No Content`.

## Change password

```http
PUT /api/user/password
```

### Request

| Field | Type | Rules |
| --- | --- | --- |
| `current_password` | string | Required and must match the account password. |
| `password` | string | Required and must satisfy the configured Laravel password rules. |
| `password_confirmation` | string | Required to match `password`. |

```json
{
  "current_password": "old-password",
  "password": "new-secure-password",
  "password_confirmation": "new-secure-password"
}
```

Returns `204 No Content`.

Existing device tokens remain valid after the password changes.

## Update one setting

```http
PATCH /api/user/settings/single
```

```json
{
  "setting": "library.view",
  "value": "grid"
}
```

Both fields are required; `value` cannot be `null`.

Known settings and values are:

| Setting | Accepted value |
| --- | --- |
| `library.view` | `grid`, `list`, or `shelf` |
| `library.tilt_books` | boolean |
| `single_book.default_section` | `notes` or `reviews` |
| `profile.colour` | Hex colour |

Unknown setting names are currently accepted.

Missing `setting` or `value` returns `422` with an `errors` object and no top-level `message`. A known setting that fails its model-backed rule may use Laravel's standard validation envelope.

### Success: `200 OK`

```json
{
  "message": "User settings updated successfully.",
  "setting": "library.view",
  "value": "grid"
}
```

## Update multiple settings

```http
PATCH /api/user/settings/multiple
```

Send a non-empty object of setting names and values:

```json
{
  "settings": {
    "library.view": "list",
    "library.tilt_books": false
  }
}
```

### Success: `200 OK`

```json
{
  "success": true,
  "message": "User settings updated successfully."
}
```

An invalid or empty `settings` input returns `422` with `success: false`, a `message`, and an `errors` object. A known setting that fails its model-backed rule may use Laravel's standard validation envelope.

## Delete account

```http
DELETE /api/user
```

### Request

```json
{
  "password": "current-password"
}
```

`password` is required and must match the current password. A successful request returns `204 No Content`, revokes every personal access token belonging to the user, and permanently deletes the account.
