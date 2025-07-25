<?php

declare(strict_types=1);

namespace App\Enums;

enum AnalyticsEvent: string
{
    case SettingUpdated = 'setting.updated';
    case MultipleSettingsUpdated = 'settings.updated.multiple';

    case BookAdded = 'book.added';
    case BookRemoved = 'book.removed';
    case BookStatusUpdated = 'book.status.updated';

    case BookPreviewed = 'book.previewed';
    case BookCoverUpdated = 'book.cover.updated';
    case BookCoverRemoved = 'book.cover.removed';

    case BookNoteAdded = 'book.note.added';
    case BookNoteUpdated = 'book.note.updated';
    case BookNoteRemoved = 'book.note.removed';

    case BookRatingAdded = 'book.rating.added';
    case BookRatingUpdated = 'book.rating.updated';
    case BookRatingRemoved = 'book.rating.removed';

    case BookReviewAdded = 'book.review.added';
    case BookReviewUpdated = 'book.review.updated';
    case BookReviewRemoved = 'book.review.removed';

    case UserAccountCreated = 'user.account.created';
    case UserAccountDeleted = 'user.account.deleted';
    case UserAccountAvatarRemoved = 'user.account.avatar.removed';
    case UserAccountAvatarUpdated = 'user.account.avatar.updated';
    case UserAccountProfileColourUpdated = 'user.account.profile_colour.updated';
}
