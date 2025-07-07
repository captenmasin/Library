<?php

declare(strict_types=1);

namespace App\Enums;

enum ActivityType: string
{
    case BookAdded = 'book.added';
    case BookStatusUpdated = 'book.status.updated';
    case BookRemoved = 'book.removed';

    case BookNoteAdded = 'book.note.added';
    case BookNoteUpdated = 'book.note.updated';
    case BookNoteRemoved = 'book.note.removed';

    case BookReviewAdded = 'book.review.added';
    case BookReviewUpdated = 'book.review.updated';
    case BookReviewRemoved = 'book.review.removed';

    case BookCoverUpdated = 'book.cover.updated';
    case BookCoverRemoved = 'book.cover.removed';
}
