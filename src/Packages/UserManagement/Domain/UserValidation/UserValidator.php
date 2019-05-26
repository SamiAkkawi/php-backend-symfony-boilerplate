<?php declare(strict_types=1);

namespace App\Packages\UserManagement\Domain\UserValidation;

use App\Utilities\Validation\Messages\CanNotBeChosenMessage;
use App\Utilities\Validation\Messages\DoesAlreadyExistMessage;
use App\Utilities\Validation\Rules\MaxLengthRule;
use App\Utilities\Validation\Rules\MinLengthRule;
use App\Utilities\Validation\Rules\RequiredEmailAddressRule;
use App\Utilities\Validation\Rules\RequiredUuidRule;
use App\Utilities\Validation\Rules\RequiredStringRule;
use App\Utilities\Validation\Validator;
use App\Packages\UserManagement\Application\CreateUser;
use App\Resources\User\EmailAddress;
use App\Resources\User\Password;
use App\Resources\User\UserId;
use App\Resources\User\Username;
use App\Resources\UserRole\RoleId;
use App\Utilities\AuthUser;

final class UserValidator extends Validator
{
    private $usersWithAnEqualValueQuery;

    public function __construct(UsersWithAnEqualValueQuery $usersWithAnEqualValueQuery)
    {
        parent::__construct();
        $this->usersWithAnEqualValueQuery = $usersWithAnEqualValueQuery;
    }

    public function validateCreation(CreateUser $command): void
    {
        $this->resetMessageBags();
        $this->validateUserIdFormat($command->getUserId());
        $this->validateUsernameFormat($command->getUsername());
        $this->validateEmailAddressFormat($command->getEmailAddress());
        $this->validateRoleId($command->getRoleId(), $command->getExecutor());
        $this->validatePassword($command->getPassword());
        $validateForExistingUser = false;
        $this->validateExistingUsers(
            $command->getUserId(), $command->getUsername(), $command->getEmailAddress(), $validateForExistingUser
        );
    }

    private function validateUserIdFormat(string $userId): void
    {
        $error = RequiredUuidRule::findError($userId);
        if ($error !== null) {
            $this->errors->addMessage(UserId::getKey(), $error);
            return;
        }
    }

    private function validateUsernameFormat(string $username): void
    {
        $errorMessage = RequiredStringRule::findError($username);
        if ($errorMessage !== null) {
            $this->errors->addMessage(Username::getKey(), $errorMessage);
            return;
        }

        $minLength = 4;
        $errorMessage = MinLengthRule::findError($username, $minLength);
        if ($errorMessage !== null) {
            $this->errors->addMessage(Username::getKey(), $errorMessage);
            return;
        }

        $maxLength = 32;
        $errorMessage = MaxLengthRule::findError($username, $maxLength);
        if ($errorMessage !== null) {
            $this->errors->addMessage(Username::getKey(), $errorMessage);
        }
    }

    private function validateEmailAddressFormat(string $emailAddress): void
    {
        $errorMessage = RequiredEmailAddressRule::findError($emailAddress);
        if ($errorMessage !== null) {
            $this->errors = $this->errors->addMessage(EmailAddress::getKey(), $errorMessage);
            return;
        }

        $minLength = 4;
        $errorMessage = MinLengthRule::findError($emailAddress, $minLength);
        if ($errorMessage !== null) {
            $this->errors = $this->errors->addMessage(Username::getKey(), $errorMessage);
            return;
        }

        $maxLength = 191;
        $errorMessage = MaxLengthRule::findError($emailAddress, $maxLength);
        if ($errorMessage !== null) {
            $this->errors = $this->errors->addMessage(Username::getKey(), $errorMessage);
        }
    }

    private function validateRoleId(string $roleId, AuthUser $authUser): void
    {
        $errorMessage = RoleId::findFormatError($roleId);
        if ($errorMessage !== null) {
            $this->errors = $this->errors->addMessage(RoleId::getKey(), $errorMessage);
            return;
        }

        $availableRoleIds = [AuthUser::NORMAL_USER_ROLE_ID];
        if ($authUser->isAdmin() || $authUser->isSystem()) {
            $availableRoleIds[] = [AuthUser::ADMIN_USER_ROLE_ID];
        }

        if (!in_array($roleId, $availableRoleIds)) {
            $this->errors = $this->errors->addMessage(RoleId::getKey(), new CanNotBeChosenMessage());
        }
    }

    private function validatePassword(string $password): void
    {
        $errorMessage = Password::findFormatError($password);
        if ($errorMessage !== null) {
            $this->errors = $this->errors->addMessage(Password::getKey(), $errorMessage);
        }
    }

    private function validateExistingUsers(
        string $userId,
        string $username,
        string $emailAddress,
        bool $validateForExistingUser
    ): void
    {
        if ($this->errors->hasOneOfKeys([UserId::getKey(), Username::getKey(), EmailAddress::getKey()])) {
            return;
        }
        $userIdToUse = UserId::fromString($userId);
        $usernameToUse = Username::fromString($username);
        $emailAddressToUse = EmailAddress::fromString($emailAddress);
        $users = $this->usersWithAnEqualValueQuery->execute($userIdToUse, $usernameToUse, $emailAddressToUse);
        foreach ($users->toArray() as $user) {
            if ($validateForExistingUser && $user->getUserId()->isEqual($userIdToUse)) {
                continue;
            }
            if (!$validateForExistingUser && $user->getUserId()->isEqual($userIdToUse)) {
                $this->errors = $this->errors->addMessage(UserId::getKey(), new DoesAlreadyExistMessage());;
            }
            if ($user->getUsername()->isEqual($usernameToUse)) {
                $this->errors = $this->errors->addMessage(Username::getKey(), new DoesAlreadyExistMessage());
            }
            if ($user->getEmailAddress()->isEqual($emailAddressToUse)) {
                $this->errors = $this->errors->addMessage(EmailAddress::getKey(), new DoesAlreadyExistMessage());
            }
        }
    }
}