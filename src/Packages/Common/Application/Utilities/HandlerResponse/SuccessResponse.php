<?php declare(strict_types=1);

namespace App\Packages\Common\Application\Utilities\HandlerResponse;

use App\Packages\Common\Application\Utilities\Validation\Messages\MessageBag;

final class SuccessResponse implements Success
{
    private $warnings;

    private function __construct(MessageBag $warnings)
    {
        $this->warnings = $warnings;
    }

    public static function create(): self
    {
        $warnings = MessageBag::create();
        return new self($warnings);
    }

    public function getWarnings(): MessageBag
    {
        return $this->warnings;
    }
}