<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Finder\SplFileInfo;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class PictureController extends AbstractController
{
    private string $uploadDir;
    const PER_PAGE = 18;

    public function __construct(ParameterBagInterface $bag)
    {
        $this->uploadDir = $bag->get('uploads_dir');
    }

    /**
     * @Route("/pictures", name="list-pictures", methods={"GET"})
     */
    public function index(Request $request)
    {
        $user = $this->getUser();
        $photoshoot = $user->getPhotoShoot();

        $dir = realpath($this->uploadDir . '/' . $photoshoot->getId());
        $finder = new Finder();
        $finder->files()->in($dir);
        $finder->sortByName(true);

        $page = (int) $request->query->get('page', 1);
        $totalItems = $finder->count();
        $results = array_map(fn (SplFileInfo $file) => $file->getFileName(), array_values(iterator_to_array($finder)));

        $pagination = [
            'results' => [],
            'pagination' => [
                'total' => $totalItems,
                'maxPage' => ceil($totalItems / self::PER_PAGE),
                'currentPage' => $page,
                'perPage' => self::PER_PAGE,
            ],
        ];

        $offset = ($page - 1) * self::PER_PAGE;

        foreach (array_slice($results, $offset, self::PER_PAGE) as $key => $file) {
            $pagination['results'][$offset + $key + 1] = $file;
        }
        
        return $this->json($pagination);
    }

    /**
     * @Route("/pictures/range", name="pictures-range", methods={"GET"})
     */
    public function range(Request $request)
    {
        $name = $request->query->get('file', null);
        $index = (int) $request->query->get('index', null);
        $user = $this->getUser();
        $photoshoot = $user->getPhotoShoot();

        $dir = realpath($this->uploadDir . '/' . $photoshoot->getId());
        $finder = new Finder();
        $finder->files()->in($dir);
        $finder->sortByName(true);

        $totalItems = $finder->count();
        $results = array_map(fn (SplFileInfo $file) => $file->getFilename(), array_values(iterator_to_array($finder)));

        if (null !== $name) {
            $index = array_search($name, $results);
        }
        
        $page = (int) floor($index / self::PER_PAGE);
        $offset = $page * self::PER_PAGE;

        $range = [
            'results' => [],
            'total' => $totalItems,
        ];

        foreach (array_slice($results, $offset, self::PER_PAGE) as $key => $file) {
            $range['results'][$offset + $key + 1] = $file;
        }

        return $this->json($range);
    }

    /**
     * @Route("/pictures/{name}", name="get-picture", methods={"GET"})
     */
    public function getPicture(string $name)
    {
        $user = $this->getUser();
        $photoshoot = $user->getPhotoShoot();

        $dir = realpath($this->uploadDir . '/' . $photoshoot->getId());
        $filename = realpath(sprintf('%s/%s', $dir, $name));

        return new BinaryFileResponse($filename);
    }
}
