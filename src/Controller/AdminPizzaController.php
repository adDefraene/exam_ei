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
        // Init the object
        $pizza = new Pizza();
        // Get the ingredients to give them for the js treatment : get a select with all of them
        $ingredients = $repo->findAll();

        // Init the form
        $form = $this->createForm(AdminPizzaType::class, $pizza);
        $form->handleRequest($req);

        // hHen form submitted & OK
        if($form->isSubmitted() && $form->isValid())
        {   
            // Get the image data
            $file = $form['image']->getData();
            // If not empty
            if(!empty($file)){
                // Get a new image treatment
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
            
            // Defacto set its type to "CLASSIC"
            $pizza->setType("CLASSIC");

            // Persist & Flush
            $manager->persist($pizza);
            $manager->flush();

            // Flash
            $this->addFlash(
                "success",
                "La pizza <strong>{$pizza->getName()}</strong> a bien été ajoutée !"
            );

            // Redirect
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
        // Init the form
        $form = $this->createForm(AdminPizzaType::class, $pizza);
        $form->handleRequest($req);

        // Get current image ICO no changes for the image
        $currentFile = $pizza->getImage();

        // If form submitted & OK
        if($form->isSubmitted() && $form->isValid())
        {   
            // Resets the slug
            $pizza->setSlug("");

            // Get the image data
            $file = $form['image']->getData();
            // If not empty
            if(!empty($file)){
                // New IMG treatment
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
                // If not images provided
                if(!empty($currentFile)){
                    // Resets it with the old image
                    $pizza->setImage($currentFile);
                }
            }

            // Persist & Flush
            $manager->persist($pizza);
            $manager->flush();

            // Flash
            $this->addFlash(
                "success",
                "La pizza <strong>{$pizza->getName()}</strong> a bien été modifiée !"
            );

            // Redirect
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
        // Get all the pizzas, in order to generate their select tag
        $pizzas = $repo->findAll();   

        // Init the form
        $form = $this->createForm(AdminPromoType::class);
        $form->handleRequest($req);

        // If Form submitted & OK
        if($form->isSubmitted() && $form->isValid()){
            // Reset the pizza's type PROMO back to CLASSIC
            // Get the pizza in promo
            $pizzasToReset = $repo->getSpecialPizzas("PROMO", 3);
            // For each one...
            foreach($pizzasToReset as $pizzaToReset){
                // Set them back to "CLASSIC"
                $pizzaToReset->setType("CLASSIC");
                // Persist it
                $manager->persist($pizzaToReset);
            }
            // Flush it once
            $manager->flush();

            // Sets the choosen ones to "PROMO"
            // Decode first the sent array
            $pizzaToResetJson = json_decode($form->getData()['pizzasToPromo']);
            // For each one of the cells
            foreach($pizzaToResetJson as $pizzasId){
                // Get the choosen pizza
                $pizzaToSetAgain = $repo->findPizzaById($pizzasId);
                // Set it to "PROMO"
                $pizzaToSetAgain->setType("PROMO");
                // Persist it
                $manager->persist($pizzaToSetAgain);
            }
            // Flush it twice
            $manager->flush();
        }
    
        // Flash
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
