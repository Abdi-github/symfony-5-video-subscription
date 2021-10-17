<?php

namespace App\Controller;

use App\Repository\CategoryRepository;
use App\Repository\VideoRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HomeContollerController extends AbstractController
{
    #[Route('/', name: 'home')]
    public function index(Request $request, VideoRepository $vr, CategoryRepository $cr): Response
    {
        $categories = $cr->findBy([], ['id' => 'ASC']);

        $newVideos = $vr->newVideosByLimit(8);

        // \dd($newVideos);

        return $this->render('home/index.html.twig', [
            'newVideos' => $newVideos,
            'categories' => $categories,
        ]);
    }
}
