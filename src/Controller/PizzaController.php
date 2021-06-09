<?php

namespace App\Controller;

use App\Entity\Pizza;
use App\Repository\PizzaRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class PizzaController extends AbstractController
{
    /**
     * @Route("/menu", name="menu")
     */
    public function index(PizzaRepository $repo): Response
    {
        $pizzas = $repo->findAll();

        return $this->render('pizza/menu.html.twig', [
            'pizzas' => $pizzas,
        ]);
    }

    /**
     * @Route("/pizza/{slug}", name="pizza_show")
     */
    public function show(Pizza $pizza): Response
    {
        return $this->render('pizza/show.html.twig', [
            'pizza' => $pizza,
        ]);
    }
}
