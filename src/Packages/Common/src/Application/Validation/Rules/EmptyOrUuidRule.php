<?php declare(strict_types=1);

namespace App\Packages\Common\Application\Validation\Messages\Rules;

use App\Packages\Common\Application\Validation\Messages\Message;
use App\Packages\Common\Application\Validation\Messages\MustBeAStringMessage;
use App\Packages\Common\Application\Validation\Messages\MustBeAUuidMessage;
use App\Packages\Common\Application\Validation\Rules\Rule;
use Ramsey\Uuid\Uuid;

final class EmptyOrUuidRule implements Rule
{
    public static function getMessageFromValidation($data): ?Message
    {
        if(!is_string($data)) {
            return new MustBeAStringMessage();
        }
        if(strlen($data) === 0) {
            return null;
        }
        if(!Uuid::isValid((string)$data)) {
            return new MustBeAUuidMessage();
        }
        return null;
    }
}