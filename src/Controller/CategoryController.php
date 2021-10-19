<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\CategoryRepository;
use App\Repository\VideoRepository;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

class CategoryController extends AbstractController
{
    // #[Route('/category', name: 'category')]
    // public function index(): Response
    // {
    //     return $this->render('category/index.html.twig', [
    //         'controller_name' => 'CategoryController',
    //     ]);
    // }




    public function getCategories(CategoryRepository $cr)
    {
        $categories = $cr->findBy([], ['id' => 'ASC']);

        return $this->render('common/_categories.html.twig', [
            'categories' => $categories
        ]);
    }

    #[Route('/videos/{category_name}/{category_id}', name: 'videos.by.category')]
    public function getVideosByCategory(Request $request, CategoryRepository $cr, VideoRepository $vr): Response
    {



        // dd($request->query->get('title'));
        // $sort_criteria='created_at';
        // $sort_method="DESC";
        // $category_id = $request->attributes->get('category_id');
        // dd($request->attributes->get('category_id'));
        // $videosByCategory=$vr->findBy(['category'=>$category_id]);

        $page = (int)$request->query->get('page', 1);



        $limit = 3;

        $sortByTitle = $request->query->get('title');
        $sortByRating = $request->query->get('rating');




        if ($sortByTitle) {
            if ($sortByTitle == 'DESC') {
                $category_id = $request->attributes->get('category_id');

                $sort_method = "DESC";
                $sort_criteria = 'name';
                $videosByCategoryLimited = $vr->getPaginatedVideos($page, $limit, $category_id, $sort_criteria, $sort_method);
            } else if ($sortByTitle == 'ASC') {
                $category_id = $request->attributes->get('category_id');

                $sort_method = "ASC";
                $sort_criteria = 'name';
                $videosByCategoryLimited = $vr->getPaginatedVideos($page, $limit, $category_id, $sort_criteria, $sort_method);
            }
        } else if ($sortByRating) {
            if ($sortByRating == 'DESC') {
                $category_id = $request->attributes->get('category_id');

                $sort_method = "DESC";
                $sort_criteria = 'rating';
                $videosByCategoryLimited = $vr->getPaginatedVideos($page, $limit, $category_id, $sort_criteria, $sort_method);
            } else if ($sortByRating == 'ASC') {
                $category_id = $request->attributes->get('category_id');

                $sort_method = "ASC";
                $sort_criteria = 'rating';
                $videosByCategoryLimited = $vr->getPaginatedVideos($page, $limit, $category_id, $sort_criteria, $sort_method);
            }
        } else if ($sortByTitle == null && $sortByTitle == null) {
            $category_id = $request->attributes->get('category_id');

            $sort_method = "DESC";
            $sort_criteria = 'created_at';
            $videosByCategoryLimited = $vr->getPaginatedVideos($page, $limit, $category_id, $sort_criteria, $sort_method);
        }




        $videosByCategoryAll = $vr->getCategorizedTotalVideos($category_id);







        $category_name = $request->attributes->get('category_name');




        return $this->render('category/index.html.twig', [
            'videosByCategoryLimited' => $videosByCategoryLimited,
            'total' => $videosByCategoryAll,
            'category_name' => $category_name,
            'page' => $page,
            'limit' => $limit,
            'category_id' => $category_id,
            'sort_title_method' => $sortByTitle,
            'sort_rating_method' => $sortByRating,


        ]);
    }
}
