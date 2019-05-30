<?php declare(strict_types=1);

namespace App\Packages\UserManagement\Domain\Handlers;

use App\Packages\Common\Application\JobQueuing\QueueCommand;
use App\Packages\Common\Domain\CommandHandler;
use App\Packages\Common\Domain\DidNotReceiveSuccessResponseException;
use App\Packages\UserManagement\Application\CreateUser;
use App\Utilities\HandlerResponse\Success;
use App\Packages\UserManagement\Application\SendVerificationCodeToUser;
use App\Packages\UserManagement\Domain\UserRepository;
use App\Packages\UserManagement\Domain\UserValidation\UserValidator;
use App\Resources\User\EmailAddress;
use App\Resources\User\Password;
use App\Resources\User\UserId;
use App\Resources\User\Username;
use App\Resources\UserRole\RoleId;
use App\Utilities\HandlerResponse\Response;
use App\Utilities\HandlerResponse\ValidationErrorResponse;
use App\Utilities\HandlerResponse\ResourceCreatedResponse;
use App\Packages\UserManagement\Domain\UserAggregate;
use App\Utilities\AuthUserFactory;

final class CreateUserHandler
{
    private $validator;
    private $userRepository;
    private $authUserFactory;
    private $commandHandler;

    public function __construct(
        UserValidator $validator,
        UserRepository $userRepository,
        AuthUserFactory $authUserFactory,
        CommandHandler $commandHandler
    ) {
        $this->validator = $validator;
        $this->userRepository = $userRepository;
        $this->authUserFactory = $authUserFactory;
        $this->commandHandler = $commandHandler;
    }

    public function handle(CreateUser $command): Response
    {
        $this->validator->validateCreation($command);
        if ($this->validator->hasErrors()) {
            return new ValidationErrorResponse(
                $this->validator->getErrors(),
                $this->validator->getWarnings()
            );
        }
        $userAggregate = UserAggregate::create(
            UserId::fromString($command->getUserId()),
            Username::fromString($command->getUsername()),
            EmailAddress::fromString($command->getEmailAddress()),
            Password::fromString($command->getPassword()),
            RoleId::fromString($command->getRoleId()),
            $command->getExecutor()
        );
        $this->userRepository->save($userAggregate);
        if ($command->sendInvitation()) {
            $this->queueSendVerificationCode($command);
        }
        return new ResourceCreatedResponse($this->validator->getWarnings());
    }

    private function queueSendVerificationCode(CreateUser $command): void
    {
        $systemAuthUser = $this->authUserFactory->createSystemUser();
        $commandToQueue = SendVerificationCodeToUser::fromUserId($command->getUserId(), $systemAuthUser);
        $createJobCommand = QueueCommand::create($commandToQueue, $systemAuthUser);
        $response = $this->commandHandler->handle($createJobCommand);
        if (!$response instanceof Success) {
            throw new DidNotReceiveSuccessResponseException(
                'Could not create job: ' . print_r($response, true)
            );
        }
    }
}