<?php

namespace App\Controller;

use App\Repository\PhotoPackageRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

class PhotoPackageController extends AbstractController
{
    /**
     * @Route("/photo-package", name="photoshoot-package-list", methods={"GET"})
     */
    public function listPhotoPackages(PhotoPackageRepository $photoPackageRepository): JsonResponse
    {
        return $this->json(
            $photoPackageRepository->findAll(),
            JsonResponse::HTTP_OK,
            [],
            ['groups' => 'photo-package'],
        );
    }
}
