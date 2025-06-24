<?php

namespace App\Contracts;

interface BookApiServiceInterface
{
    public static function search(?string $query = null, ?string $author = null, int $maxResults = 15, int $pageIndex = 0): array;

    public static function get(string $id): ?array;

    public static function getByCode(string $isbn): ?array;

    public static function getFromApi(string $id): ?array;
}
