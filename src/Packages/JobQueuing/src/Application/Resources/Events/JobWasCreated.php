<?php declare(strict_types=1);

namespace App\Packages\JobQueuing\Application\Resources\Events;

use App\Packages\Common\Application\Authorization\User\User as AuthUser;
use App\Packages\Common\Domain\Events\AbstractEvent;
use App\Packages\Common\Domain\Events\EventId;
use App\Packages\Common\Domain\Events\OccurredAt;
use App\Packages\JobQueuing\Application\Resources\Job\Job;

final class JobWasCreated extends AbstractEvent
{
    public static function occur(Job $job, AuthUser $authUser): self
    {
        $previousPayload = null;
        $occurredAt = OccurredAt::create();
        $payload = JobPayload::fromJob($job, [
            'createdAt' => $occurredAt->toString()
        ]);
        return new self(EventId::create(), $occurredAt, $authUser, $payload, $previousPayload);
    }

    public function getJob(): Job
    {
        /** @var $payload JobPayload */
        $payload = $this->getPayload();
        return $payload->toJob();
    }

    public function mustBeLogged(): bool
    {
        return false;
    }
}