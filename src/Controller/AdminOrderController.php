<?php

namespace App\Controller;

use App\Entity\Order;
use App\Service\PaginationService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class AdminOrderController extends AbstractController
{
    /**
     * @Route("/admin/order/{page<\d+>?1}", name="admin_order_index")
     */
    public function index($page, PaginationService $pagination): Response
    {
        $pagination->setEntityClass(Order::class)
                 ->setPage($page)
                 ->setLimit(10)
                 ->setRoute('admin_order_index');
 
        return $this->render('admin/order/index.html.twig', [
            'pagination' => $pagination
        ]);
    }
    
      
    /**
     * Deletes an order
     * @Route("/admin/order/{id}/delete", name="admin_order_delete")
     *
     * @param User $user
     * @param EntityManagerInterface $manager
     * @return Response
     */
    public function delete(Order $order, EntityManagerInterface $manager){

        $this->addFlash(
            'success',
            "La commande du <strong>{$order->getDate()->format("Y-m-d H:i")}</strong> a bien été supprimée"
        );

        $manager->remove($order->getOrderItems());
        $manager->remove($order);
        $manager->flush();

        return $this->redirectToRoute('admin_order_index');
    }
}
