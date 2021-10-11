<?php

namespace App\Controller;

use App\Entity\Ingredient;
use App\Form\RegisterType;
use App\Form\AdminIngredientType;
use App\Service\PaginationService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;

class AdminIngredientController extends AbstractController
{
    /**
     * Displays all the ingredients
     * 
     * @Route("/admin/ingredients/{page<\d+>?1}", name="admin_ingredient_index")
     */
    public function index($page, PaginationService $pagination): Response
    {
       $pagination->setEntityClass(Ingredient::class)
                ->setPage($page)
                ->setLimit(10)
                ->setRoute('admin_ingredient_index');

        return $this->render('admin/ingredient/index.html.twig', [
            'pagination' => $pagination
        ]);
    }

    /**
      * Adds an ingredient
      *
      * @Route("/admin/ingredients/add", name="admin_ingredient_add")
      *
      * @param Ingredient $ingredient
      * @param Request $req
      * @param EntityManagerInterface $manager
      * @return Response
      */
    public function add(Request $req, EntityManagerInterface $manager): Response
    {
        $ingredient = new Ingredient();

        $form = $this->createForm(AdminIngredientType::class, $ingredient);
        $form->handleRequest($req);

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

                $ingredient->setImage($newFilename);
            }
            $manager->persist($ingredient);
            $manager->flush();

            $this->addFlash(
                "success",
                "L'ingrédient <strong>{$ingredient->getName()}</strong> a bien été ajouté !"
            );

            return $this->redirectToRoute('admin_ingredient_index');
        }

        return $this->render('admin/ingredient/add.html.twig', [
            'ingredient' => $ingredient,
            'myForm' => $form->createView()
        ]);
    }

    /**
      * Modifies an ingredient
      *
      * @Route("/admin/ingredients/{id}/edit", name="admin_ingredient_edit")
      *
      * @param Ingredient $ingredient
      * @param Request $req
      * @param EntityManagerInterface $manager
      * @return Response
      */
    public function edit(Ingredient $ingredient, Request $req, EntityManagerInterface $manager): Response
    {
        $form = $this->createForm(AdminIngredientType::class, $ingredient);
        $form->handleRequest($req);

        if($form->isSubmitted() && $form->isValid())
        {
            $manager->persist($ingredient);
            $manager->flush();

            $this->addFlash(
                "success",
                "L'ingrédient <strong>{$ingredient->getName()}</strong> a bien été modifié !"
            );
        }

        return $this->render('admin/ingredient/edit.html.twig', [
            'ingredient' => $ingredient,
            'myForm' => $form->createView()
        ]);
    }
    
    /**
     * Deletes an ingredient
     * @Route("/admin/ingredient/{id}/delete", name="admin_ingredient_delete")
     *
     * @param User $user
     * @param EntityManagerInterface $manager
     * @return Response
     */
    public function delete(Ingredient $ingredient, EntityManagerInterface $manager){
        /*
        if(count($ingredient->getPizzas()) > 0){
            $this->addFlash(
                "warning",
                "Vous ne pouvez pas supprimer l'ingrédient <strong>{$ingredient->getName()}</strong> car celui-ci est dans une pizza disponible !"
            );
        }else{
        */
            $this->addFlash(
                'success',
                "L'ingrédient <strong>{$ingredient->getName()}</strong> a bien été supprimé(e)"
            );
            $manager->remove($ingredient);
            $manager->flush();

        //}

        return $this->redirectToRoute('admin_ingredient_index');
    }
}