<?php

namespace App\Actions\Books;

use Illuminate\Support\Collection;
use Lorisleiva\Actions\Concerns\AsAction;

class ImportBooksFromArray
{
    use AsAction;

    public function handle(array|Collection|null $books): void
    {
        if (! $books) {
            return;
        }

        if (is_array($books)) {
            $books = collect($books);
        }

        foreach ($books as $book) {
            if (empty($book['identifier'])) {
                continue;
            }

            ImportBookFromData::dispatch(
                $book['identifier'],
                $book
            );
        }
    }
}
