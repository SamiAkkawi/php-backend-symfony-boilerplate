<?php declare(strict_types=1);

namespace App\Packages\UserManagement\Domain\User;

use App\Packages\Common\Application\Authorization\User as AuthUser;
use App\Packages\Common\Domain\Event\EventStream;
use App\Packages\Common\Domain\ResourceManager;
use App\Packages\UserManagement\Domain\User\Events\UserWasCreated;
use App\Packages\UserManagement\Application\Resources\User\User;

final class UserManager extends ResourceManager
{
    private $persistedUser;
    private $currentUser;

    protected function __construct(EventStream $recordedEvents, User $currentUser, ?User $persistedUser)
    {
        parent::__construct($recordedEvents);
        $this->currentUser = $currentUser;
        $this->persistedUser = $persistedUser;
    }

    public static function manage(User $user): self
    {
        return new self(new EventStream([]), $user, $user);
    }

    public function getUser(): User
    {
        return $this->currentUser;
    }

    public static function create(User $user, AuthUser $creator): self
    {
        $persistedUser = null;
        return new self(
            new EventStream([
                UserWasCreated::occur($user, $creator)
            ]),
            $user,
            $persistedUser
        );
    }
}