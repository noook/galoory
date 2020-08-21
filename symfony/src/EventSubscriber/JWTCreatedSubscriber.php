<?php

namespace App\EventSubscriber;

use App\Repository\UserRepository;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class JWTCreatedSubscriber implements EventSubscriberInterface
{
    private UserRepository $userRepository;

    public function __construct(UserRepository $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    public function onLexikJwtAuthenticationOnJwtCreated($event)
    {
        $payload = $event->getData();
        $user = $this->userRepository->findOneBy(['email' => $payload['username']]);
        $payload['firstname'] = $user->getFirstname();
        $payload['lastname'] = $user->getLastname();

        $event->setData($payload);
    }

    public static function getSubscribedEvents()
    {
        return [
            'lexik_jwt_authentication.on_jwt_created' => 'onLexikJwtAuthenticationOnJwtCreated',
        ];
    }
}
