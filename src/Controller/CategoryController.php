<?php

namespace App\Controller;

use App\Repository\CategoryRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class CategoryController extends AbstractController
{
    #[Route('/category', name: 'category')]
    public function index(): Response
    {
        return $this->render('category/index.html.twig', [
            'controller_name' => 'CategoryController',
        ]);
    }

    public function getCategories(CategoryRepository $cr)
    {
        $categories = $cr->findBy([], ['id' => 'ASC']);

        return $this->render('common/_categories.html.twig', [
            'categories' => $categories
        ]);
    }
}
