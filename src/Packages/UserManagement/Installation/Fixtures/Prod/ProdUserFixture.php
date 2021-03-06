<?php declare(strict_types=1);

namespace App\Packages\UserManagement\Installation\Fixtures\Prod;

use App\Packages\AccessManagement\Application\Query\AuthUser;
use App\Packages\AccessManagement\Application\ResourceAttributes\AuthUser\LanguageId;
use App\Packages\Common\Application\Command\Params\Text;
use App\Packages\Common\Domain\DidNotReceiveSuccessResponseException;
use App\Packages\UserManagement\Application\Command\User\CreateUser;
use App\Packages\AccessManagement\Application\ResourceAttributes\AuthUser\RoleId;
use App\Packages\Common\Application\Command\CommandBus;
use App\Packages\Common\Application\Utilities\HandlerResponse\Success;
use App\Packages\Common\Installation\Fixtures\Fixture;
use App\Packages\UserManagement\Application\Command\User\UserParams;
use App\Packages\UserManagement\Application\ResourceAttributes\User\EmailAddress;
use App\Packages\UserManagement\Application\ResourceAttributes\User\Password;
use App\Packages\UserManagement\Application\ResourceAttributes\User\UserId;
use App\Packages\UserManagement\Application\ResourceAttributes\User\Username;

final class ProdUserFixture extends Fixture
{
    private $commandBus;

    public function __construct(CommandBus $commandBus)
    {
        $this->commandBus = $commandBus;
    }

    public function execute(): void
    {
        $authUser = AuthUser::system(LanguageId::english());
        foreach($this->getRows() as $row) {
            $command = CreateUser::fromArray([
                CreateUser::USER_PARAMS => UserParams::fromArray([
                    UserId::class => Text::fromString($row['id']),
                    Username::class => Text::fromString($row['username']),
                    EmailAddress::class => Text::fromString($row['emailAddress']),
                    Password::class => Text::fromString($row['password']),
                    RoleId::class => Text::fromString($row['roleId']),
                ]),
                CreateUser::USER_MUST_BE_VERIFIED => false,
                CreateUser::CREATOR => $authUser,
            ]);
            $response = $this->commandBus->handle($command);
            if(!$response instanceof Success) {
                throw new DidNotReceiveSuccessResponseException(
                    'Error response from fixture.'
                    . "\n" . ' Data:' . "\n" . print_r($row, true)
                    . "\n" . ' Response:' . "\n" . print_r($response, true)
                );
            }
        }
    }

    private function getRows(): array
    {
        return [
            [
                'id' => '11111111-1111-1111-1111-111111111111',
                'username' => getenv('APP_ADMIN_USERNAME'),
                'emailAddress' => getenv('APP_ADMIN_EMAIL'),
                'password' => getenv('APP_ADMIN_PASSWORD'),
                'roleId' => RoleId::admin()->toString(),
            ]
        ];
    }
}