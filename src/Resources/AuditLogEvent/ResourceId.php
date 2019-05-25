<?php declare(strict_types=1);

namespace App\Resources\AuditLogEvent;

use App\Resources\Attribute;

final class AggregateId implements Attribute
{
    private $id;

    public static function getKey(): string
    {
        return 'auditLogEvent.aggregateId';
    }

    private function __construct(string $id)
    {
        $this->id = $id;
    }

    public static function fromString(string $id): self
    {
        return new self($id);
    }

    public function toString(): string
    {
        return $this->id;
    }
}