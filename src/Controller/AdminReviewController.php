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

    /**
      * Modifies a review
      *
      * @Route("/admin/reviews/{id}/edit", name="admin_reviews_edit")
      *
      * @param Review $review
      * @param Request $req
      * @param EntityManagerInterface $manager
      * @return Response
      */
    public function edit(Review $review, Request $req, EntityManagerInterface $manager): Response
    {
        $form = $this->createForm(AdminReviewType::class, $review);
        $form->handleRequest($req);

        if($form->isSubmitted() && $form->isValid())
        {
            $manager->persist($review);
            $manager->flush();

            $this->addFlash(
                "success",
                "L'évaluation <strong>{$review->getReview()}</strong> a bien été modifiée !"
            );
        }

        return $this->render('admin/review/edit.html.twig', [
            'review' => $review,
            'myForm' => $form->createView()
        ]);
    }
    
    /**
     * Deletes a review
     * @Route("/admin/review/{id}/delete", name="admin_reviews_delete")
     *
     * @param Review $review
     * @param EntityManagerInterface $manager
     * @return Response
     */
    public function delete(Review $review, EntityManagerInterface $manager){
        $this->addFlash(
            'success',
            "L'utilisateur <strong>{$review->getReview()}</strong> a bien été supprimé(e)"
        );
        $manager->remove($review);
        $manager->flush();

        return $this->redirectToRoute('admin_reviews_index');

    }
}
