<?php

namespace App\Controller;

use App\Entity\PhotoShoot;
use App\Entity\SelectedPicture;
use App\Repository\SelectedPictureRepository;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

class SelectedPictureController extends AbstractController
{
    /**
     * @Route("/selection", name="get-selection", methods={"GET"})
     */
    public function index()
    {
        /** @var \App\Entity\User */
        $user = $this->getUser();
        $photoshoot = $user->getPhotoShoot();
    
        $selectedFiles = array_map(fn (SelectedPicture $file) => $file->getFilename(), $photoshoot->getSelectedPictures()->toArray());
        natsort($selectedFiles);

        return $this->json($selectedFiles);
    }

    /**
     * @Route("/selection/quota", name="get-selection-quota", methods={"GET"})
     */
    public function getQuota()
    {
        /** @var \App\Entity\User */
        $user = $this->getUser();
        $quantity = $user->getPhotoShoot()->getQuantity();

        return $this->json([
            'quota' => $quantity,
        ]);
    }

    /**
     * @Route("/selection/{filename}", name="add-to-selection", methods={"POST"})
     */
    public function addToSelection(
        SelectedPictureRepository $selectedPictureRepository,
        EntityManagerInterface $em,
        string $filename
    )
    {
        /** @var \App\Entity\User */
        $user = $this->getUser();
        $photoshoot = $user->getPhotoShoot();

        $foundPic = $selectedPictureRepository->findOneBy([
            'photoshoot' => $photoshoot->getId(),
            'filename' => $filename,
        ]);

        if (null !== $foundPic) {
            return $this->json(
                [
                    'message' => 'File already in selection.',
                    'file' => $filename,
                ],
                JsonResponse::HTTP_CONFLICT,
            );
        }

        $picture = new SelectedPicture;
        $picture
            ->setPhotoshoot($photoshoot)
            ->setFilename($filename);
        
        $em->persist($picture);
        $em->flush();

        return $this->json(
            $picture,
            JsonResponse::HTTP_CREATED,
            [],
            ['groups' => 'selected-picture']
        );
    }

    /**
     * @Route("/selection/{filename}", name="remove-from-selection", methods={"DELETE"})
     */
    public function remoeFromSelection(
        SelectedPictureRepository $selectedPictureRepository,
        EntityManagerInterface $em,
        string $filename
    )
    {
        /** @var \App\Entity\User */
        $user = $this->getUser();
        $photoshoot = $user->getPhotoShoot();

        $foundPic = $selectedPictureRepository->findOneBy([
            'photoshoot' => $photoshoot->getId(),
            'filename' => $filename,
        ]);

        if (null === $foundPic) {
            return $this->json(
                [
                    'message' => 'File not in selection.',
                    'file' => $filename,
                ],
                JsonResponse::HTTP_BAD_REQUEST,
            );
        }
        
        $em->remove($foundPic);
        $em->flush();

        return $this->json(null, JsonResponse::HTTP_NO_CONTENT);
    }
}
