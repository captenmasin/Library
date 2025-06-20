<?php

declare(strict_types=1);

namespace App\Enums;

enum UserPermission: string
{
    // General admin
    case CREATE_USER = 'create user';
    case VIEW_ADMIN_PANEL = 'view admin panel';
    case VIEW_HORIZON_PANEL = 'view horizon panel';
    case VIEW_TELESCOPE_PANEL = 'view telescope panel';
    case VIEW_PULSE_PANEL = 'view pulse panel';
    case VIEW_ANALYTICS = 'view analytics';
}
