<?php

namespace App\Actions\Books;

use Lorisleiva\Actions\Concerns\AsAction;

class ImportBooksFromArray
{
    use AsAction;

    public function handle(?array $books)
    {
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
