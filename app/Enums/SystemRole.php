<?php

namespace App\Enums;

enum SystemRole: string
{
    case CEO = 'CEO';
    case PROJECT_MANAGER = 'Project Manager';
    case SYSTEM_ANALYST = 'Sistem Analis';
    case PROGRAMMER = 'Programmer';
    case DEVOPS = 'DevOps';
    case UI_UX = 'UI/UX';
    case MARKETING = 'Marketing';
    case QA = 'QA';

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}
