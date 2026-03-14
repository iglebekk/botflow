<?php

namespace App\Enums;

enum TaskStatus: string
{
    case Open = 'open';
    case InProgress = 'in_progress';
    case Blocked = 'blocked';
    case Done = 'done';
    case Cancelled = 'cancelled';

    public function isClosed(): bool
    {
        return match ($this) {
            self::Done, self::Cancelled => true,
            default => false,
        };
    }
}
