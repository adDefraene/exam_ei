<?php

namespace App\Controller;

use App\Service\StatsService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AdminDashboardController extends AbstractController
{
    /**
     * @Route("/admin/dashboard", name="admin_dashboard")
     */
    public function index(StatsService $service): Response
    {
        $users = $service->getUsersCount();
        $pizzas = $service->getPizzaCount();
        $ingredients = $service->getIngredientCount();
        $orders = $service->getOrderCount();
        $reviews = $service->getReviewsCount();
/*
        $bestReviews = $service->getReviewsStats("ASC");
        $worstReviews = $service->getReviewsStats("DESC");
*/

        return $this->render('admin/dashboard/index.html.twig', [
            'stats' => [
                'users' => $users,
                'pizzas' => $pizzas,
                'ingredients' => $ingredients,
                'orders' => $orders,
                'reviews' => $reviews,
            ],
            /*
            'bestReviews' => $bestReviews,
            'worstReviews' => $worstReviews
            */

        ]);
    }
}
