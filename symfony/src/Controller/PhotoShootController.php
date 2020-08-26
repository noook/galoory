<?php

namespace App\Controller;

use App\Action\CreatePhotoshoot;
use App\Action\CreateUser;
use App\Action\SaveFile;
use App\Entity\PhotoShoot;
use App\Form\UserRegisterType;
use App\Repository\PhotoPackageRepository;
use App\Repository\PhotoShootRepository;
use App\Repository\UserRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Messenger\Stamp\HandledStamp;
use Symfony\Component\Routing\Annotation\Route;

class PhotoShootController extends AbstractController
{
    /**
     * @Route("/photoshoot", name="list-photoshoots", methods={"GET"})
     */
    public function listPhotoshoots(PhotoShootRepository $photoShootRepository): JsonResponse
    {
        return $this->json(
            $photoShootRepository->findAll(),
            JsonResponse::HTTP_OK,
            [],
            ['groups' => ['photoshoot', 'user', 'photo-package']]
        );
    }

    /**
     * @Route("/photoshoot", name="new-photoshoot", methods={"POST"})
     */
    public function newPhotoshoot(
        Request $request,
        PhotoPackageRepository $photoPackageRepository,
        UserRepository $userRepository,
        MessageBusInterface $commandBus
    ): JsonResponse
    {
        $payload = \json_decode($request->getContent(), true);

        $requiredFields = ['user', 'package', 'expiration'];
        
        foreach ($requiredFields as $field) {
            if (!isset($payload[$field])) {
                return $this->json([
                    'message' => sprintf('Missing field "%s"', $field),
                ], JsonResponse::HTTP_BAD_REQUEST);
            }
        }

        $package = $photoPackageRepository->find($payload['package']);

        if (null === $package) {
            return $this->json([
                'message' => sprintf('Invalid value "%s" for field "package"', $payload['package']),
            ], JsonResponse::HTTP_BAD_REQUEST);
        }

        if (is_string($payload['user'])) {
            $user = $userRepository->find($payload['user']);
            if (null === $user) {
                return $this->json([
                    'message' => sprintf('Invalid value "%s" for field "user"', $payload['user']),
                ]);
            }
        } else {
            $userPayload = array_merge(
                $payload['user'],
                ['password' => 'password']
            );
            $form = $this->createForm(UserRegisterType::class, $userPayload);
            $form->submit($userPayload);

            try {
                $envelope = $commandBus->dispatch(
                    new CreateUser(
                        $userPayload['email'],
                        $userPayload['firstname'],
                        $userPayload['lastname'],
                        $userPayload['password']
                    )
                );
                $user = $envelope->last(HandledStamp::class)->getResult();
            } catch (\Exception $e) {
                return $this->json([
                    'message' => $e->getMessage(),
                ], JsonResponse::HTTP_CONFLICT);
            }
        }

        $expiration = new \DateTime($payload['expiration']);

        $envelope = $commandBus->dispatch(
            new CreatePhotoshoot(
                $user,
                $package,
                $expiration
            )
        );

        $photoshoot = $envelope->last(HandledStamp::class)->getResult();

        return $this->json(
            $photoshoot,
            JsonResponse::HTTP_CREATED,
            [],
            ['groups' => ['photoshoot', 'user', 'photo-package']]
        );
    }

    /**
     * @Route("/photoshoot/{photoshoot}", name="get-photoshoot", methods={"GET"})
     * @ParamConverter("photoshoot", class="App\Entity\PhotoShoot")
     */
    public function getPhotoshoot(PhotoShoot $photoshoot): JsonResponse
    {
        return $this->json(
            $photoshoot,
            JsonResponse::HTTP_OK,
            [],
            ['groups' => ['photoshoot', 'user', 'photo-package']]
        );
    }

    /**
     * @Route("/photoshoot/{photoshoot}/files", name="photoshoot-files", methods={"POST"})
     * @ParamConverter("photoshoot", class="App\Entity\PhotoShoot")
     */
    public function uploadFiles(Request $request, PhotoShoot $photoshoot, MessageBusInterface $commandBus): JsonResponse
    {
        $files = $request->files->get('files', []);

        if (count($files) > 15) {
            return $this->json([
                'message' => 'Too much files. Upload limited to 15 files, please use a .zip archive.'
            ], JsonResponse::HTTP_BAD_REQUEST);
        }

        foreach ($files as $file) {
            $commandBus->dispatch(new SaveFile($file, $photoshoot));
        }

        return $this->json([], JsonResponse::HTTP_NO_CONTENT, []);
    }
}
