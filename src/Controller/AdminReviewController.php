<?php

namespace App\Controller;

use App\Entity\Review;
use App\Form\AdminReviewType;
use App\Service\PaginationService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class AdminReviewController extends AbstractController
{
    /**
     * Displays all the reviews
     * 
     * @Route("/admin/reviews/{page<\d+>?1}", name="admin_reviews_index")
     */
    public function index($page, PaginationService $pagination): Response
    {
       $pagination->setEntityClass(Review::class)
                ->setPage($page)
                ->setLimit(10)
                ->setRoute('admin_reviews_index');

        return $this->render('admin/review/index.html.twig', [
            'pagination' => $pagination
        ]);
    }
}
