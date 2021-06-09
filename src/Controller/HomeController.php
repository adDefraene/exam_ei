<?php

namespace App\Controller;

use App\Repository\PizzaRepository;
use App\Repository\ReviewRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController
{
    /**
     * @Route("/", name="home")
     */
    public function index(ReviewRepository $reviewRepo, PizzaRepository $pizzaRepo): Response
    {
        $homeReviews = $reviewRepo->getLastSix();
        $promoPizzas = $pizzaRepo->getSpecialPizzas("PROMO", 3);
        $potm = $pizzaRepo->getSpecialPizzas("POTM", 1);

        return $this->render('home/index.html.twig', [
            'reviews' => $homeReviews,
            'promosPizzas' => $promoPizzas,
            'potm' => $potm
        ]);
    }
}
