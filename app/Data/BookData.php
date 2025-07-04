<?php

namespace App\Data;

use Spatie\LaravelData\Data;

class BookData extends Data
{
    public function __construct(
        public string $identifier,
        public ?string $title,
        public ?string $path,
        public ?string $description,
        public ?string $description_clean,
        public ?string $published_date,
        public ?array $categories,
        public ?int $page_count,
        public ?array $publisher,
        public ?array $authors,
        public ?string $cover,
        public ?string $service = null,

        public ?array $codes = [],
        public ?bool $in_library = null,
        public ?string $user_status = null,
        public ?array $user_tags = [],
        public ?bool $has_custom_cover = null,
        public ?string $colour = null,
        public ?string $created_at = null,
        public ?string $updated_at = null,
        public ?array $links = [],
    ) {}
}
