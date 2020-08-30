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
use Symfony\Component\HttpFoundation\Request;
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
        Request $request,
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

        $page = $request->query->get('page', 1);
        $totalItems = $finder->count();
        $perPage = 18;
        $results = iterator_to_array($finder);

        $pagination = [
            'results' => [],
            'pagination' => [
                'total' => $totalItems,
                'maxPage' => floor($totalItems / $perPage),
                'currentPage' => $page,
                'perPage' => $perPage,
            ],
        ];

        $offset = $page * $perPage;

        foreach (array_slice($results, $offset, $perPage) as $file) {
            $pagination['results'][] = $file->getFilename();
        }
        
        return $this->json($pagination);
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
