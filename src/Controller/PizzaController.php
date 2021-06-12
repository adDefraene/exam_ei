<?php

namespace App\Controller;

use App\Entity\Order;
use App\Entity\Pizza;
use App\Form\OrderType;
use App\Entity\OrderItem;
use App\Repository\IngredientRepository;
use App\Repository\PizzaRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

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

    
    /**
     * Makes the orders
     * 
     * @Route("/order", name="order")
     * 
     * @IsGranted("ROLE_USER")
     */
    public function order(Request $request, EntityManagerInterface $manager, PizzaRepository $pizzaRepo, IngredientRepository $ingredientRepo): Response
    {
        $pizzas = $pizzaRepo->findAll();
        $ingredients = $ingredientRepo->findAll();

        $order = new Order();
        $user = $this->getUser();

        $form = $this->createForm(OrderType::class, $order);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){
            $order->setCustomer($user)
                ->setState("ORDERED")
            ;
            
            // MESSAGE FOR THE MAIL
            $formMessage = "";
            $orderTotal = 0;

            $orderItems = json_decode($order->getOrderItemsJson());
            
            foreach($orderItems as $orderItem){
                $item = new OrderItem();

                $itemPrice = 0;

                $formMessage .= "<b>".$pizzaRepo->findPizzaById($orderItem->pizzaId)->getName()."</b>";
                $itemPrice .= $pizzaRepo->findPizzaById($orderItem->pizzaId)->getPrice();

                $item->setItemPizza($pizzaRepo->findPizzaById($orderItem->pizzaId));
                foreach($orderItem->ingredients as $ingredient){
                    $item->addSupIngredient($ingredientRepo->findIngredientById($ingredient));
                    $formMessage .= "<i>+ ".$ingredientRepo->findIngredientById($ingredient)->getName()."</i>";
                    $itemPrice .= $ingredientRepo->findIngredientById($ingredient)->getPrice();
                }

                $formMessage .= "<u> = ".$itemPrice." </u><br>";
                $orderTotal .= $itemPrice;

                $manager->persist($item);
                $order->addOrderItem($item);
                
            }

            if($order->getIfDelivered()){
                $formMessage .= "<br><b>La commande sera livrée (+3€) à :";
                $orderTotal .= 3;
            } else {
                $formMessage .= "<br><b>La commande sera à emporter à :";
            }
            $formMessage .= "<u>".$order->getDate()->format('Y-m-d h:i') . "</u></b>";

            $formMessage .= "<br><br>TOTAL : ".$orderTotal."€";

            $manager->persist($order);

        // SEND EMAIL

        //VARs
            $lineScan = "\n";
            $boundary = "-----=".md5(rand());

            $formName = $user->getFirstName()." ".$user->getLastName();
        
        //MY EMAIL ADDRESS
            $formMail = $user->getEmail();
            $myMail = "adriendefraene@gmail.com";
            
        //SUBJECT OF THE MAIL
            $mailSubject = " Commande de Pizzle's - ". $order->getDate()->format('Y-m-d h:i');
            
        //HEADER OF THE MAIL
            $header = "From: \"Pizzle's\"<hey@adriendefraene.be>".$lineScan;
            $header .= "Reply-to:\"".$formName."\"<".$user->getEmail().">".$lineScan;
            $header .= "MIME-Version: 1.0".$lineScan;
            $header .= "Content-Type: multipart/alternative;".$lineScan." boundary=\"".$boundary."\"".$lineScan;

        //DEFINING THE MESSAGE
            $message_txt = "Cher client, voici votre commande :".$formMessage;
            $message_html = "<html><head></head><body>Cher client, voici votre commande :<br>".$formMessage."</body></html>";

        //DEFINING THE MESSAGE TO BE SENT
            //ADD MESSAGE IN TXT FORMAT
            $messageFinal = $lineScan."--".$boundary.$lineScan;
            $messageFinal .= "Content-Type: text/plain; charset=\"ISO-8859-1\"".$lineScan;
            $messageFinal .= "Content-Transfer-Encoding: 8bit".$lineScan;
            $messageFinal .= $lineScan.$message_txt.$lineScan;
            //ADD MESSAGE IN HTML FORMAT
            $messageFinal .= $lineScan."--".$boundary.$lineScan;
            $messageFinal .= "Content-Type: text/html; charset=\"ISO-8859-1\"".$lineScan;
            $messageFinal .= "Content-Transfer-Encoding: 8bit".$lineScan;
            $messageFinal .= $lineScan.$message_html.$lineScan;
            //END
            $messageFinal .= $lineScan."--".$boundary."--".$lineScan;
            $messageFinal .= $lineScan."--".$boundary."--".$lineScan;

            mail($myMail, $mailSubject, $messageFinal, $header);
            
            $manager->flush();

            $this->addFlash(
                'success',
                'Votre commande a bien été prise !'
            );

            return $this->redirectToRoute('user_profile');
        }

        return $this->render('pizza/order.html.twig', [
            'orderForm' => $form->createView(),
            'pizzas' => $pizzas,
            'ingredients' => $ingredients,
        ]);
    }    
}
