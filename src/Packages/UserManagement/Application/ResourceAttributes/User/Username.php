<?php declare(strict_types=1);

namespace App\Packages\UserManagement\Application\ResourceAttributes\User;

use App\Packages\Common\Application\ResourceAttributes\Attribute;

final class Username implements Attribute
{
    private $username;

    private function __construct(string $username)
    {
        $this->username = $username;
    }

    public static function fromString(string $username): self
    {
        return new self($username);
    }

    public function toString(): string
    {
        return $this->username;
    }

    public function isEqual(self $username): bool
    {
        return (strcasecmp($username->toString(), $this->toString()) === 0);
    }

    public function isSame(self $username): bool
    {
        return ($username->toString() === $this->toString());
    }
}