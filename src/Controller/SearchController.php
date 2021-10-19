<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\VideoRepository;


class SearchController extends AbstractController
{

    #[Route('/search-result', name: 'search.result')]
    public function getSearchResult(Request $request, VideoRepository $vr): Response
    {

        // dd($request->get('query'));

        $query = $request->get('query');



        // dd($totalSearchResult);

        if ($query) {
            $videos = $vr->findVideosByName($query);
            if ($videos) {
                // dd($videos);
                return $this->render('search/index.html.twig', [
                    'videos' => $videos,


                ]);
            }
            return $this->render('search/index.html.twig', [
                'videos' => null

            ]);
        } else {
            return $this->redirectToRoute('home');
        }



        return $this->render('search/index.html.twig', [
            'controller_name' => 'SearchController',
        ]);
    }
}
