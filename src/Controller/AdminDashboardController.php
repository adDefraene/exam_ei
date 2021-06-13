<?php

namespace App\Controller;

use App\Service\StatsService;
use App\Repository\OrderRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class AdminDashboardController extends AbstractController
{
    /**
     * @Route("/admin/dashboard", name="admin_dashboard")
     */
    public function index(StatsService $service, OrderRepository $repo): Response
    {
        $users = $service->getUsersCount();
        $pizzas = $service->getPizzaCount();
        $ingredients = $service->getIngredientCount();
        $orders = $service->getOrderCount();
        $reviews = $service->getReviewsCount();

        $ordersToday = $repo->getTodayOrders();
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
            'orders' => $ordersToday

        ]);
    }

    
    /**
      * Set "READY" for an order
      *
      * @Route("/admin/{id}/ready", name="admin_order_ready")
      *
      * @param Pizza $pizza
      * @param Request $req
      * @param EntityManagerInterface $manager
      * @return Response
      */
      public function promos($id, EntityManagerInterface $manager, OrderRepository $repo): Response
      {
            $order = $repo->findOneById($id);   
            $order->setState("READY");
            $manager->persist($order);
            $manager->flush();
        
            $this->addFlash(
                "success",
                "La commande du <strong>{$order->getDate()->format("Y-m-d H:i")}</strong> a bien été préparée !"
            );

            return $this->redirectToRoute('admin_dashboard');
            
      }

}
