<?php

namespace App\Controller;

use App\Entity\Pizza;
use App\Form\AdminPizzaType;
use App\Form\AdminPromoType;
use App\Repository\IngredientRepository;
use App\Repository\PizzaRepository;
use App\Service\PaginationService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;

class AdminPizzaController extends AbstractController
{
    /**
     * @Route("/admin/pizza/{page<\d+>?1}", name="admin_pizza_index")
     */
    public function index($page, PaginationService $pagination): Response
    {
        $pagination->setEntityClass(Pizza::class)
                 ->setPage($page)
                 ->setLimit(10)
                 ->setRoute('admin_pizza_index');
 
        return $this->render('admin/pizza/index.html.twig', [
            'pagination' => $pagination
        ]);
    }

    
    /**
      * Adds an pizza
      *
      * @Route("/admin/pizza/add", name="admin_pizza_add")
      *
      * @param Pizza $pizza
      * @param Request $req
      * @param EntityManagerInterface $manager
      * @return Response
      */
      public function add(Request $req, EntityManagerInterface $manager, IngredientRepository $repo): Response
      {
            $pizza = new Pizza();
            $ingredients = $repo->findAll();

            $form = $this->createForm(AdminPizzaType::class, $pizza);
            $form->handleRequest($req);

            if($form->isSubmitted() && $form->isValid())
            {   
                $file = $form['image']->getData();
                if(!empty($file)){
                    $originalFilename = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
                    $safeFilename = transliterator_transliterate('Any-Latin; Latin-ASCII; [^A-Za-z0-9_] remove; Lower()', $originalFilename);
                    $newFilename = $safeFilename.'-'.uniqid().'.'.$file->guessExtension();
                    try{
                        $file->move(
                            $this->getParameter('uploads_directory'),
                            $newFilename
                        );
                    }
                    catch(FileException $e)
                    {
                        return $e->getMessage();
                    }

                    $pizza->setImage($newFilename);
                }
                
                $pizza->setType("CLASSIC");

                $manager->persist($pizza);
                $manager->flush();

                $this->addFlash(
                    "success",
                    "La pizza <strong>{$pizza->getName()}</strong> a bien été ajoutée !"
                );

                return $this->redirectToRoute('admin_pizza_index');
            }

            return $this->render('admin/pizza/add.html.twig', [
                'pizza' => $pizza,
                'myForm' => $form->createView(),
                'ingredients' => $ingredients
            ]);
      }
    
    /**
      * Edits an pizza
      *
      * @Route("/admin/pizza/{id}/edit", name="admin_pizza_edit")
      *
      * @param Pizza $pizza
      * @param Request $req
      * @param EntityManagerInterface $manager
      * @return Response
      */
      public function edit(Pizza $pizza, Request $req, EntityManagerInterface $manager): Response
      {
            $form = $this->createForm(AdminPizzaType::class, $pizza);
            $form->handleRequest($req);
            $currentFile = $pizza->getImage();

            if($form->isSubmitted() && $form->isValid())
            {   
                $pizza->setSlug("");
                $file = $form['image']->getData();
                if(!empty($file)){
                    $originalFilename = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
                    $safeFilename = transliterator_transliterate('Any-Latin; Latin-ASCII; [^A-Za-z0-9_] remove; Lower()', $originalFilename);
                    $newFilename = $safeFilename.'-'.uniqid().'.'.$file->guessExtension();
                    try{
                        $file->move(
                            $this->getParameter('uploads_directory'),
                            $newFilename
                        );
                    }
                    catch(FileException $e)
                    {
                        return $e->getMessage();
                    }

                    $pizza->setImage($newFilename);
                }else{
                    if(!empty($currentFile)){
                        $pizza->setImage($currentFile);
                    }
                }

                $manager->persist($pizza);
                $manager->flush();

                $this->addFlash(
                    "success",
                    "La pizza <strong>{$pizza->getName()}</strong> a bien été modifiée !"
                );

                return $this->redirectToRoute('admin_pizza_index');
            }

            return $this->render('admin/pizza/edit.html.twig', [
                'pizza' => $pizza,
                'myForm' => $form->createView(),
            ]);
      }
    
    /**
      * Selects the Pizzas PROMOS
      *
      * @Route("/admin/pizza/promos", name="admin_pizza_promos")
      *
      * @param Pizza $pizza
      * @param Request $req
      * @param EntityManagerInterface $manager
      * @return Response
      */
      public function promos(Request $req, EntityManagerInterface $manager, PizzaRepository $repo): Response
      {
            $pizzas = $repo->findAll();   

            $form = $this->createForm(AdminPromoType::class);
            $form->handleRequest($req);

            if($form->isSubmitted() && $form->isValid()){
                $pizzasToReset = $repo->getSpecialPizzas("PROMO", 3);
                foreach($pizzasToReset as $pizzaToReset){
                    $pizzaToReset->setType("CLASSIC");
                    $manager->persist($pizzaToReset);
                }
                $manager->flush();
                $pizzaToResetJson = json_decode($form->getData()['pizzasToPromo']);
                foreach($pizzaToResetJson as $pizzasId){
                    $pizzaToSetAgain = $repo->findPizzaById($pizzasId);
                    $pizzaToSetAgain->setType("PROMO");
                    $manager->persist($pizzaToSetAgain);
                    $manager->flush();
                }
            }
        
            $this->addFlash(
                "success",
                "Les pizzas en promotions ont bien été modifiées !"
            );

            return $this->render('admin/pizza/promo.html.twig', [
                'myForm' => $form->createView(),
                'pizzas' => $pizzas,
            ]);
      }
    
      
    /**
     * Deletes an pizza
     * @Route("/admin/pizza/{id}/delete", name="admin_pizza_delete")
     *
     * @param User $user
     * @param EntityManagerInterface $manager
     * @return Response
     */
    public function delete(Pizza $pizza, EntityManagerInterface $manager){

            $this->addFlash(
                'success',
                "La pizza <strong>{$pizza->getName()}</strong> a bien été supprimée"
            );
            $manager->remove($pizza);
            $manager->flush();

        //}

        return $this->redirectToRoute('admin_pizza_index');
    }


}
