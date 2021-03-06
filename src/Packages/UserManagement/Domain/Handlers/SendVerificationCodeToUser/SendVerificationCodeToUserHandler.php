<?php declare(strict_types=1);

namespace App\Packages\UserManagement\Domain\Handlers\SendVerificationCodeToUser;

use App\Packages\Common\Domain\Mailing\Mailer;
use App\Packages\UserManagement\Application\Command\User\SendVerificationCodeToUser;
use App\Packages\UserManagement\Domain\UserEventDispatcher;
use App\Packages\UserManagement\Application\ResourceAttributes\User\UserId;
use App\Packages\Common\Application\Utilities\HandlerResponse\ResourceNotFoundResponse;
use App\Packages\Common\Application\Utilities\HandlerResponse\Response;
use App\Packages\Common\Application\Utilities\HandlerResponse\SuccessResponse;

final class SendVerificationCodeToUserHandler
{
    private $userEventDispatcher;
    private $mailer;

    public function __construct(UserEventDispatcher $userEventDispatcher, Mailer $mailer)
    {
        $this->userEventDispatcher = $userEventDispatcher;
        $this->mailer = $mailer;
    }

    public function handle(SendVerificationCodeToUser $command): Response
    {
        //todo
        /*
        $user = $this->userByIdQuery->execute(UserId::fromString($command->getUserId()));
        if($user === null) {
            return ResourceNotFoundResponse::create();
        }
        $subject = 'Verification';
        $link = getenv('APP_WEBFRONTEND_VERIFICATION_URL');
        $link = str_replace('%verificationCode%', urlencode($command->getVerificationCode()), $link);
        $content = str_replace('%link%', $link, file_get_contents('./template.html'));
        $this->mailer->send($user->getEmailAddress()->toString(), $subject, $content);
        */
        return SuccessResponse::create();
    }
}