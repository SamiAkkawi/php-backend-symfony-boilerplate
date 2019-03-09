<?php declare(strict_types=1);

namespace App\Packages\Common\Domain;

use App\Packages\Common\Domain\Events\AbstractEvent;

interface Projection
{
    public function when(AbstractEvent $event): void;
}