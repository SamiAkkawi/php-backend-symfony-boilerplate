<?php declare(strict_types=1);

namespace App\Packages\Common\Application\Command;

interface Command
{
    public static function getHandlerClass(): string;
}