<?php

namespace App\Controller;

use App\Entity\User;
use App\Entity\Order;
use App\Entity\Review;
use App\Form\ReviewType;
use App\Form\ProfileType;
use App\Form\RegisterType;
use App\Entity\PasswordUpdate;
use App\Form\PasswordUpdateType;
use App\Repository\UserRepository;
use Symfony\Component\Form\FormError;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class UserController extends AbstractController
{
    /**
     * Allows us to log in
     *
     * @Route("/login", name="user_login")
     * 
     * @param AuthenticationUtils $utils
     * @return Response
     */
    public function index(AuthenticationUtils $utils): Response
    {
        $error = $utils->getLastAuthenticationError();
        $username = $utils->getLastUsername();

        return $this->render('user/login.html.twig', [
            'hasError' => $error !== null,
            'username' => $username
        ]);
    }

    /**
     * Allows us to disconnect
     *
     * @Route("/logout", name="user_logout")
     */
    public function logout(){
        //Silence is golden
    }

    /**
     * Register form
     * 
     * @Route("/register", name="user_register")
     */
    public function register(Request $request, EntityManagerInterface $manager, UserPasswordEncoderInterface $encoder): Response
    {
        $user = new User();
        $form = $this->createForm(RegisterType::class, $user);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){

            // crypter le mot de passe avec l'encoder
            $hash = $encoder->encodePassword($user, $user->getPassword());
            $user->setPassword($hash);

            $manager->persist($user);
            $manager->flush();
        
            $this->addFlash(
                'success',
                'Votre compte a bien été créé ! Connectez-vous maintenant.'
            );
            
            return $this->redirectToRoute('user_login');
        }


        return $this->render('user/register.html.twig', [
            'registerForm'  => $form->createView(),
        ]);
    }

    
    /**
     * Allows us to consult our profile
     *
     * @Route("/mon_compte", name="user_profile")
     * @IsGranted("ROLE_USER")
     * 
     * @return Response
     */
    public function profile(): Response
    {
        return $this->render('user/account.html.twig', [
            'user' => $this->getUser()
        ]);
    }
    
    /**
     * Allows us to modify our profile
     *
     * @Route("/mon_compte/modifier", name="user_profile_modify")
     * @IsGranted("ROLE_USER")
     * 
     * @return Response
     */
    public function profileModify(Request $request, EntityManagerInterface $manager): Response
    {
        $user = $this->getUser();

        $form = $this->createForm(ProfileType::class, $user);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid())
        {
            $manager->persist($user);
            $manager->flush();

            $this->addFlash(
                'success',
                'Votre compte a été modifié correctment !'
            );

            return $this->redirectToRoute('user_profile');
        }

        return $this->render('user/profileModify.html.twig', [
            'user'              => $user,
            'profileModifyForm' => $form->createView()
        ]);
    }

    
    /**
     * Allows us to add a review
     *
     * @Route("/mon_compte/review/{id}", name="user_review")
     * @IsGranted("ROLE_USER")
     * 
     * @return Response
     */
    public function profileReview(Order $order, Request $request, EntityManagerInterface $manager): Response
    {
        $ifReview = $order->getReview();
        if($ifReview || $order->getState() !== "DELIVERED"){
            $this->addFlash(
                'danger',
                'Cette évaluation a déjà été réalisée !'
            );

            return $this->redirectToRoute('user_profile');
        }

        $review = new Review();
        $form = $this->createForm(ReviewType::class, $review);
        $form->handleRequest($request);
        
        if($form->isSubmitted() && $form->isValid()){

            $review->setReviewedOrder($order);

            $manager->persist($review);
            $manager->flush();

            $this->addFlash(
                'success',
                'Votre évaluation a été enregistrée !'
            );

            return $this->redirectToRoute('user_profile');
        }

        return $this->render('user/review.html.twig', [
            'reviewForm' => $form->createView(),
            'order' => $order
        ]);
    }
    
    /**
     * Allows us to update our password
     *
     * @Route("/mon_compte/modify_password", name="user_password")
     * @IsGranted("ROLE_USER")
     * 
     * @return Response
     */
    public function profileModifyPassword(Request $request, EntityManagerInterface $manager, UserPasswordEncoderInterface $encoder): Response
    {
        $passwordUpdate = new PasswordUpdate();
        $user = $this->getUser();

        $form = $this->createForm(PasswordUpdateType::class, $passwordUpdate);
        $form->handleRequest($request);
        
        if($form->isSubmitted() && $form->isValid()){
            // Check if the old passord is good
            if(!password_verify($passwordUpdate->getOldPassword(),$user->getPassword())){
                $form->get('oldPassword')->addError(new FormError("Le mot de passe que vous avez tapé n'est pas votre mot de passe actuel"));

                $this->addFlash(
                    'danger',
                    'Le mot de passe que vous avez tapé n\'est pas votre mot de passe actuel !'
                );

            }else{
                $newPassword = $passwordUpdate->getNewPassword();
                $hash = $encoder->encodePassword($user, $newPassword);

                $user->setPassword($hash);

                $manager->persist($user);
                $manager->flush();

                $this->addFlash(
                    'success',
                    'Votre mot de passe a bien été modifié !'
                );

                return $this->redirectToRoute('user_profile');
            }
        }
        return $this->render('user/password.html.twig', [
            'passwordForm' => $form->createView(),
        ]);
    }

}
