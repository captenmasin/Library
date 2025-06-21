<?php

namespace App\Enums;

enum UserBookStatus: string
{
    case PlanToRead = 'plan_to_read';
    case Reading = 'reading';
    case Completed = 'completed';
    case OnHold = 'on_hold';
    case Dropped = 'dropped';

    public function label(): string
    {
        return match ($this) {
            self::PlanToRead => 'Plan to Read',
            self::Reading => 'Reading',
            self::Completed => 'Completed',
            self::OnHold => 'On Hold',
            self::Dropped => 'Dropped',
        };
    }

    public static function values(): array
    {
        return array_map(fn ($status) => $status->value, self::cases());
    }

    public static function options(): array
    {
        return array_map(
            fn ($status) => [
                'value' => $status->value,
                'label' => $status->label(),
            ],
            self::cases()
        );
    }
}
