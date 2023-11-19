<?php

namespace App\Enums;

use Filament\Support\Contracts\HasLabel;

enum NavigationGroup implements HasLabel
{
    case Articles;
    case UsersAndPermission;

    public function getLabel(): ?string
    {
        return match ($this) {
            self::Articles => 'Articles',
            self::UsersAndPermission => 'Users & Permissions' // Change to translation nav.shield
        };
    }

}
