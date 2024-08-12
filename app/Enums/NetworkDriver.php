<?php

namespace App\Enums;

use Filament\Support\Contracts\HasLabel;

enum NetworkDriver: string implements HasLabel
{
    case Bridge = 'bridge';
    case Overlay = 'overlay';

    public function getLabel(): string
    {
        return match ($this) {
            self::Bridge => 'Bridge',
            self::Overlay => 'Overlay'
        };
    }
}
