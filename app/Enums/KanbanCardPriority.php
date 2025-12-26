<?php

namespace App\Enums;

enum KanbanCardPriority: string
{
    case LOW = 'low';
    case MEDIUM = 'medium';
    case HIGH = 'high';

    public function label(): string
    {
        return match ($this) {
            self::LOW => 'Low',
            self::MEDIUM => 'Medium',
            self::HIGH => 'High',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::LOW => 'bg-emerald-100 text-emerald-700',
            self::MEDIUM => 'bg-amber-100 text-amber-700',
            self::HIGH => 'bg-red-100 text-red-700',
        };
    }
}
