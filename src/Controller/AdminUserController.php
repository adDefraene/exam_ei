<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\RegisterType;
use App\Form\AdminUserType;
use App\Service\PaginationService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class AdminUserController extends AbstractController
{
    /**
     * Displays all the users
     * 
     * @Route("/admin/user/{page<\d+>?1}", name="admin_user_index")
     */
    public function index($page, PaginationService $pagination): Response
    {
       $pagination->setEntityClass(User::class)
                ->setPage($page)
                ->setLimit(10)
                ->setRoute('admin_user_index');

        return $this->render('admin/user/index.html.twig', [
            'pagination' => $pagination
        ]);
    }

    /**
      * Modifies an user
      *
      * @Route("/admin/user/{id}/edit", name="admin_user_edit")
      *
      * @param User $user
      * @param Request $req
      * @param EntityManagerInterface $manager
      * @return Response
      */
    public function edit(User $user, Request $req, EntityManagerInterface $manager, UserPasswordEncoderInterface $encoder): Response
    {
        $form = $this->createForm(AdminUserType::class, $user);
        $form->handleRequest($req);

        $oldPassword = $user->getPassword();

        if($form->isSubmitted() && $form->isValid())
        {
            if($form->getData()->getPassword() === ""){
                $user->setPassword($oldPassword);
            }else{
                $newPassword = $form->getData()->getPassword();
                $hash = $encoder->encodePassword($user, $newPassword);

                $user->setPassword($hash);
            }

            $manager->persist($user);
            $manager->flush();

            $this->addFlash(
                "success",
                "L'utilisateur <strong>{$user->getEvaluationName()}</strong> a bien été modifié(e) !"
            );
        }

        return $this->render('admin/user/edit.html.twig', [
            'user' => $user,
            'myForm' => $form->createView()
        ]);
    }
    
    /**
     * Deletes an user
     * @Route("/admin/ads/{id}/delete", name="admin_user_delete")
     *
     * @param User $user
     * @param EntityManagerInterface $manager
     * @return Response
     */
    public function delete(User $user, EntityManagerInterface $manager){
        // don't delete a user whom had orders
        if(count($user->getOrders()) > 0){
            $this->addFlash(
                "warning",
                "Vous ne pouvez pas supprimer l'utilisateur <strong>{$user->getEvaluationName()}</strong> car il/elle possède déjà des commandes"
            );
        }else{
            $this->addFlash(
                'success',
                "L'utilisateur <strong>{$user->getEvaluationName()}</strong> a bien été supprimé(e)"
            );
            $manager->remove($user);
            $manager->flush();

        }

        return $this->redirectToRoute('admin_user_index');

    }
}
