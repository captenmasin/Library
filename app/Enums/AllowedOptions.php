<?php

declare(strict_types=1);

namespace App\Enums;

enum AllowedOptions: string
{
    case Width = 'width';
    case Height = 'height';
    case Format = 'format';
    case Quality = 'quality';
    case Blur = 'blur';
    case Contrast = 'contrast';
    case Flip = 'flip';
    case Version = 'version';
    case Background = 'background';
    case Scale = 'scale';
    case Crop = 'crop';
    case Pixelate = 'pixelate';
    case Rotate = 'rotate';

    public static function all(): array
    {
        return array_map(fn (self $option) => $option->value, self::cases());
    }

    public static function withTypes(): array
    {
        return [
            self::Width->value => 'integer',
            self::Height->value => 'integer',
            self::Format->value => 'string',
            self::Quality->value => 'integer',
            self::Blur->value => 'integer',
            self::Contrast->value => 'integer',
            self::Flip->value => 'string',
            self::Version->value => 'integer',
            self::Background->value => 'string',

            self::Scale->value => 'string',
            self::Crop->value => 'string',
            self::Pixelate->value => 'integer',
            self::Rotate->value => 'integer',
        ];
    }
}
