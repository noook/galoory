<?php

namespace App\Controller;

use App\Repository\UserRepository;
use Lexik\Bundle\JWTAuthenticationBundle\Security\Guard\JWTTokenAuthenticator;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Finder\SplFileInfo;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

class PictureController extends AbstractController
{
    private string $uploadDir;

    public function __construct(ParameterBagInterface $bag)
    {
        $this->uploadDir = $bag->get('uploads_dir');
    }

    /**
     * @Route("/pictures", name="list-pictures", methods={"GET"})
     */
    public function index(
        TokenStorageInterface $tokenStorage,
        UserRepository $userRepository,
        JWTTokenManagerInterface $tokenManager
    )
    {
        $token = $tokenStorage->getToken();
        $tokenInfo = $tokenManager->decode($token);

        $user = $userRepository->findOneBy(['email' => $tokenInfo['username']]);
        $photoshoot = $user->getPhotoShoot();

        $dir = realpath($this->uploadDir . '/' . $photoshoot->getId());
        $finder = new Finder();
        $finder->files()->in($dir);
        
        $files = [];

        foreach ($finder as $file) {
            $files[] = $file->getFilename();
        }
        
        return $this->json($files);
    }

    /**
     * @Route("/pictures/{name}", name="get-picture", methods={"GET"})
     */
    public function getPicture(
        TokenStorageInterface $tokenStorage,
        UserRepository $userRepository,
        JWTTokenManagerInterface $tokenManager,
        string $name
    )
    {
        $token = $tokenStorage->getToken();
        $tokenInfo = $tokenManager->decode($token);

        $user = $userRepository->findOneBy(['email' => $tokenInfo['username']]);
        $photoshoot = $user->getPhotoShoot();

        $dir = realpath($this->uploadDir . '/' . $photoshoot->getId());
        $filename = realpath(sprintf('%s/%s', $dir, $name));

        return new BinaryFileResponse($filename);
    }
}
