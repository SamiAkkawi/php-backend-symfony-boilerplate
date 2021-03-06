<?php declare(strict_types=1);

namespace App\Packages\Common\Application\Utilities\HandlerResponse;

final class ResourceNotFoundResponse implements Error
{
    private function __construct()
    {
    }

    public static function create(): self
    {
        return new self();
    }
}