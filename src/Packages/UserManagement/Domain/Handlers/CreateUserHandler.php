<?php declare(strict_types=1);

namespace App\Packages\UserManagement\Domain\Handlers;

use App\Packages\Common\Application\JobQueuing\CreateJob;
use App\Packages\Common\Domain\CommandHandler;
use App\Packages\Common\Domain\DidNotReceiveSuccessResponseException;
use App\Packages\UserManagement\Application\CreateUser;
use App\Packages\UserManagement\Application\SendVerificationCodeToUser;
use App\Packages\UserManagement\Domain\UserEventDispatcher;
use App\Packages\Resources\Application\User\Write\SaveUserParamsValidator;
use App\Resources\Application\User\Attributes\EmailAddress;
use App\Resources\Application\User\Attributes\Password;
use App\Resources\Application\User\Attributes\UserId;
use App\Resources\Application\User\Attributes\Username;
use App\Resources\Application\Application\Role\RoleId;
use App\Utilities\HandlerResponse\Response;
use App\Utilities\HandlerResponse\Success;
use App\Utilities\HandlerResponse\ValidationErrorResponse;
use App\Utilities\HandlerResponse\ResourceCreatedResponse;
use App\Packages\UserManagement\Domain\UserAggregate;
use App\Utilities\Authentication\AuthUserFactory;

final class CreateUserHandler
{
    private $validator;
    private $userEventDispatcher;
    private $authUserFactory;
    private $commandHandler;

    public function __construct(
        SaveUserParamsValidator $validator,
        UserEventDispatcher $userEventDispatcher,
        AuthUserFactory $authUserFactory,
        CommandHandler $commandHandler
    )
    {
        $this->validator = $validator;
        $this->userEventDispatcher = $userEventDispatcher;
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
        $this->userEventDispatcher->dispatchEventsFromUserAggregate($userAggregate);
        if ($command->sendInvitation()) {
            $this->queueSendVerificationCode($command);
        }
        return new ResourceCreatedResponse($this->validator->getWarnings());
    }

    private function queueSendVerificationCode(CreateUser $command): void
    {
        $systemAuthUser = $this->authUserFactory->createSystemUser();
        $commandToQueue = SendVerificationCodeToUser::create($command->getUserId(), $command->getEmailAddress(), $systemAuthUser);
        $createJobCommand = CreateJob::create($commandToQueue, $systemAuthUser);
        $response = $this->commandHandler->handle($createJobCommand);
        if (!$response instanceof Success) {
            throw new DidNotReceiveSuccessResponseException(
                'Could not create job: ' . print_r($response, true)
            );
        }
    }
}