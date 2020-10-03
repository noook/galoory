<?php

namespace App\Controller;

use App\Action\CreatePhotoshoot;
use App\Action\CreateUser;
use App\Action\DeletePhotoshoot;
use App\Action\EditPhotoshoot;
use App\Action\SaveFile;
use App\Action\SendMail;
use App\Entity\PhotoPackage;
use App\Entity\PhotoShoot;
use App\Entity\SelectedPicture;
use App\Entity\User;
use App\Form\UserRegisterType;
use App\Repository\PhotoPackageRepository;
use App\Repository\PhotoShootRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Ramsey\Uuid\Uuid;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
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
        EntityManagerInterface $em,
        MessageBusInterface $commandBus
    ): JsonResponse
    {
        $payload = \json_decode($request->getContent(), true);

        $requiredFields = ['user', 'package', 'date', 'comment'];
        
        foreach ($requiredFields as $field) {
            if (!isset($payload[$field])) {
                return $this->json([
                    'message' => sprintf('Missing field "%s"', $field),
                ], JsonResponse::HTTP_BAD_REQUEST);
            }
        }

        if (Uuid::isValid($payload['package']['id'])) {
            $package = $photoPackageRepository->find($payload['package']['id']);
        } else if ($payload['package']['id'] === 'other') {
            $package = (new PhotoPackage)
                ->setQuantity($payload['package']['quantity'])
                ->setName($payload['package']['name']);
            
            $em->persist($package);
            $em->flush();
        }

        if (null === $package) {
            return $this->json([
                'message' => sprintf('Invalid value "%s" for field "package"', $payload['package']['id']),
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
            $userPayload = $payload['user'];
            $form = $this->createForm(UserRegisterType::class, $userPayload);
            $form->submit($userPayload);

            $password = bin2hex(random_bytes(5));

            try {
                $envelope = $commandBus->dispatch(
                    new CreateUser(
                        $userPayload['email'],
                        $userPayload['firstname'],
                        $userPayload['lastname'],
                        $password
                    )
                );
                /** @var \App\Entity\User */
                $user = $envelope->last(HandledStamp::class)->getResult();
            } catch (\Exception $e) {
                return $this->json([
                    'message' => $e->getMessage(),
                ], JsonResponse::HTTP_CONFLICT);
            }

            function replaceBody(string $content, User $recipient, string $psw): string {
                return sprintf(
                    $content,
                    $recipient->getFirstname(),
                    $recipient->getEmail(),
                    $psw
                );
            }

            try {
                $body = "Bonjour %s,<br><br>" .
                        "Les photos de ton shoot seront disponibles <a href=\"http://local.nook.sh\">à cette adresse</a><br>" .
                        "Pour t'y connecter, utilise ces identifiants:<br><br>" .
                        "Email: %s<br>" .
                        "Mot de passe: %s<br><br>" .
                        "Lorsque tu auras fini ta sélection, tu pourras la valider depuis ton espace de sélection, j'en serai alors notifiée.<br><br>" .
                        "Louise";
                $altBody = "Bonjour %s,\n\n" .
                            "Les photos de ton shoot seront disponibles à cette adresse: http://local.nook.sh\n" .
                            "Pour t'y connecter, utilise ces identifiants:\n\n" .
                            "Email: %s\n>" .
                            "Mot de passe: %s\n>\n>" .
                            "Lorsque tu auras fini ta sélection, tu pourras la valider depuis ton espace de sélection, j'en serai alors notifiée.\n\n" .
                            "Louise";

                $commandBus->dispatch(
                    new SendMail(
                        'Tes photos sont disponibles !',
                        replaceBody($body, $user, $password),
                        replaceBody($altBody, $user, $password),
                        $user->getEmail(),
                    )
                );
            } catch (\Exception $e) {
                return $this->json([
                    'message' => 'An error occurred while sending the registration mail.',
                ], JsonResponse::HTTP_INTERNAL_SERVER_ERROR);
            }
        }

        $date = new \DateTime($payload['date']);

        $envelope = $commandBus->dispatch(
            new CreatePhotoshoot(
                $user,
                $package,
                $date,
                $payload['comment'],
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
     * @Route("/photoshoot/{photoshoot}", name="edit-photoshoot", methods={"PUT"})
     * @ParamConverter("photoshoot", class="App\Entity\PhotoShoot")
     */
    public function editPhotoshoot(
        Request $request,
        PhotoShoot $photoshoot,
        PhotoPackageRepository $photoPackageRepository,
        MessageBusInterface $bus,
        EntityManagerInterface $em
    ): JsonResponse
    {
        $payload = \json_decode($request->getContent(), true);

        $requiredFields = ['user', 'package', 'date', 'comment'];
        
        foreach ($requiredFields as $field) {
            if (!isset($payload[$field])) {
                return $this->json([
                    'message' => sprintf('Missing field "%s"', $field),
                ], JsonResponse::HTTP_BAD_REQUEST);
            }
        }

        if (Uuid::isValid($payload['package']['id'])) {
            $package = $photoPackageRepository->find($payload['package']['id']);
        } else if ($payload['package']['id'] === 'other') {
            $package = (new PhotoPackage)
                ->setQuantity($payload['package']['quantity'])
                ->setName($payload['package']['name']);
            
            $em->persist($package);
            $em->flush();
        }

        if (null === $package) {
            return $this->json([
                'message' => sprintf('Invalid value "%s" for field "package"', $payload['package']),
            ], JsonResponse::HTTP_BAD_REQUEST);
        }

        $date = new \DateTime($payload['date']);

        try {
            $envelope = $bus->dispatch(
                new EditPhotoshoot(
                    $photoshoot,
                    $package,
                    $payload['user'],
                    $date,
                    $payload['comment'],
                )
            );
            $photoshoot = $envelope->last(HandledStamp::class)->getResult();

            return $this->json(
                $photoshoot,
                JsonResponse::HTTP_OK,
                [],
                ['groups' => ['photoshoot', 'user', 'photo-package']]
            );
        } catch (\Exception $e) {
            return $this->json([
                'message' => $e->getMessage(),
            ], JsonResponse::HTTP_CONFLICT);
        }
    }

    /**
     * @Route("/photoshoot/{photoshoot}", name="delete-photoshoot", methods={"DELETE"})
     * @ParamConverter("photoshoot", class="App\Entity\PhotoShoot")
     */
    public function deletePhotoshoot(PhotoShoot $photoshoot, MessageBusInterface $bus): JsonResponse
    {
        try {
            $envelope = $bus->dispatch(new DeletePhotoshoot($photoshoot));
            $result = $envelope->last(HandledStamp::class)->getResult();
        } catch (\Exception $e) {
            return $this->json([
                'message' => $e->getMessage(),
            ], JsonResponse::HTTP_INTERNAL_SERVER_ERROR);
        }

        return $this->json(
            null,
            JsonResponse::HTTP_NO_CONTENT,
            [],
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

    /**
     * @Route("/photoshoot/validate", name="photoshoot-validate", methods={"POST"})
     */
    public function updateStatus(EntityManagerInterface $em): JsonResponse
    {
        /** @var \App\Entity\User */
        $user = $this->getUser();
        $photoshoot = $user->getPhotoshoot();

        $photoshoot->setStatus(PhotoShoot::STATUS_DONE);
        $em->flush();

        return $this->json(
            $photoshoot,
            JsonResponse::HTTP_OK,
            [],
            ['groups' => 'photoshoot'],
        );
    }

    /**
     * @Route("/photoshoot/{photoshoot}/export", name="photoshoot-export", methods={"GET"})
     * @ParamConverter("photoshoot", class="App\Entity\PhotoShoot")
     */
    public function exportPhotoshoot(PhotoShoot $photoshoot)
    {
        $customer = $photoshoot->getCustomer();

        $content = array_reduce(
            $photoshoot->getSelectedPictures()->toArray(),
            fn (string $acc, SelectedPicture $file) => sprintf("%s%s\n", $acc, $file->getFilename()),
            sprintf(
                "%s %s - Formule %s\n\n",
                $customer->getFirstname(),
                $customer->getLastname(),
                $photoshoot->getPackage()->getName(),
            ),
        );

        $response = new Response(
            $content,
            Response::HTTP_OK,
            [
                'Filename' => 'export.txt',
                'Content-Type' => 'text/txt',
            ]
        );

        return $response;
    }
}
