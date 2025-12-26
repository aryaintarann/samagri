<?php

namespace App\Enums;

enum InvoiceStatus: string
{
    case PENDING = 'Pending';
    case PAID = 'Paid';
    case OVERDUE = 'Overdue';
    case CANCELLED = 'Cancelled';

    public function label(): string
    {
        return $this->value;
    }

    public function color(): string
    {
        return match ($this) {
            self::PENDING => 'text-yellow-600 bg-yellow-100', // Example matching existing style
            self::PAID => 'text-green-600 bg-green-100',
            self::OVERDUE => 'text-red-600 bg-red-100',
            self::CANCELLED => 'text-gray-600 bg-gray-100',
        };
    }
}
