<?php

namespace App\Controller;

use App\Action\CreateUser;
use App\Form\UserRegisterType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Messenger\Stamp\HandledStamp;
use Symfony\Component\Routing\Annotation\Route;

class UserController extends AbstractController
{
    /**
     * @Route("/users", name="user-register", methods={"POST"})
     */
    public function register(Request $request, MessageBusInterface $commandBus): JsonResponse
    {
        $payload = json_decode($request->getContent(), true);
        $form = $this->createForm(UserRegisterType::class, $payload);
        $form->submit($payload);

        if (!$form->isValid()) {
            return $this->json([
                'message' => $form->getErrors(true)->current()->getMessage(),
            ], JsonResponse::HTTP_BAD_REQUEST);
        }

        try {
            $envelope = $commandBus->dispatch(
                new CreateUser(
                    $payload['email'],
                    $payload['firstname'],
                    $payload['password'],
                )
            );
        } catch (\Exception $e) {
            return $this->json([
                'message' => $e->getMessage(),
            ], JsonResponse::HTTP_CONFLICT);
        }

        return $this->json(
            $envelope->last(HandledStamp::class)->getResult(),
            JsonResponse::HTTP_CREATED,
            [],
            ['groups' => 'user']
        );
    }
}
