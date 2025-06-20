# Laravel Image Transformer

This is the backend implementation for the Vue `<Image>` component, using Laravel and Intervention Image.

---

## ⚙️ Configuration (`config/image-transform.php`)

### Key Settings

| Config | Description |
|--------|-------------|
| `public_path` | Prefix path where images are stored (e.g. `'storage'`) |
| `route_prefix` | URL prefix for image transform endpoint |
| `enabled_options` | List of allowed transformation options |
| `cache.enabled` | Enables disk-based caching of transformed images |
| `rate_limit.enabled` | Enables IP + path rate limiting |
| `headers` | Response headers (e.g. `Cache-Control`) |

---

## 🚀 Route Definition (`web.php`)

```php
Route::get('image-transform/{options}/{path}', ImageTransformerController::class)
    ->where('options', '([a-zA-Z]+=-?[a-zA-Z0-9]+,?)+')
    ->where('path', '.*\..*')
    ->name('image.transform');
```

This route accepts:

- `{options}`: comma-separated key=value pairs (e.g. `width=320,height=480`)
- `{path}`: image path relative to `public/storage`

---

## 🧠 Controller Logic (`ImageTransformerController`)

- Validates input path, options, and mime type
- Enforces rate limiting
- Applies transformations using `Intervention\Image`
- Caches results if enabled
- Encodes image using appropriate format encoder
- Responds with transformed image and headers

### Supported Options

| Option     | Type    | Description |
|------------|---------|-------------|
| `width`    | integer | Resize width |
| `height`   | integer | Resize height |
| `scale`    | string  | Scale instead of resize/cover (`"true"`/`"false"`) |
| `crop`     | string  | Crop alignment (`top`, `center`, etc.) |
| `blur`     | integer | Apply blur |
| `contrast` | integer | Contrast adjustment |
| `flip`     | string  | `h`, `v`, or `hv` |
| `format`   | string  | Output format override |
| `quality`  | integer | Output compression level |
| `pixelate` | integer | Pixelation amount |
| `rotate`   | integer | Rotation degrees |
| `version`  | integer | Cache busting hint |
| `background` | string | Background color for transparency blend |

---

## 🔐 Rate Limiting

Configured per IP + image path. Defaults:

- **2 requests per minute**
- Disabled in `local` and `testing` environments

---

## 📦 Caching

If enabled:
- Saves transformed image to `_cache/image-transform/...`
- Tracks with Laravel cache key for TTL expiry

---

## 🧾 Enum: `AllowedOptions`

Used to whitelist valid transformation options and expected types.

```php
public static function withTypes(): array {
    return [
        'width' => 'integer',
        'height' => 'integer',
        'format' => 'string',
        ...
    ];
}
```

---

## 🧪 Example Usage

URL:
```
/image-transform/width=320,height=480,crop=center,format=webp/books/the-hobbit.jpg
```

This resizes and crops the image with a `webp` output format.

---

## 🛡️ Safeguards

- Invalid paths or extensions → 404
- Invalid option types or values → ignored
- Invalid crop without both width + height → not applied
- Rate limits excessive transformation spam
