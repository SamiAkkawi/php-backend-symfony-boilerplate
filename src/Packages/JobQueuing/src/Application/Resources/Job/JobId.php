<?php declare(strict_types=1);

namespace App\Packages\JobQueuing\Application\Resources\Job;

use App\Packages\Common\Domain\Events\AggregateId;

final class JobId
{
    private $id;

    private function __construct(string $id)
    {
        $this->id = $id;
    }

    public static function fromString(string $id): self
    {
        return new self($id);
    }

    public function toAggregateId(): AggregateId
    {
        return AggregateId::fromString($this->id);
    }

    public function toString(): string
    {
        return $this->id;
    }

    public function isEqual(self $jobId): bool
    {
        return (strcasecmp($jobId->toString(), $this->toString()) === 0);
    }
}