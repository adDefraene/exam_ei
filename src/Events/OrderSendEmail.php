<?php 
namespace App\Events; 
use Symfony\Component\HttpKernel\KernelEvents; 
use Symfony\Component\HttpKernel\Event\ViewEvent; 
use ApiPlatform\Core\EventListener\EventPriorities;
use App\Entity\Order;
use Symfony\Component\EventDispatcher\EventSubscriberInterface; 
 
/**
 * Class that executes the "auto-value" methods from the Order class
 * => in order to be executed when adding a new one.
 */
class OrderSendEmail implements EventSubscriberInterface
{ 

    /**
     * Executes the setAutoValues() method during the Kernel Event
     *
     * @return array
     */
    public static function getSubscribedEvents() 
    { 
        return [ 
            KernelEvents::VIEW => ['setEmail', EventPriorities::POST_WRITE] 
        ]; 
    }

    /**
     * Method that executes the "autovalue" methods from the Order class when adding one
     *
     * @param ViewEvent $event
     * @return void
     */
    public function setEmail(ViewEvent  $event){ 
        $order = $event->getControllerResult(); // récupérer l'objet désérialisé   
        $method = $event->getRequest()->getMethod(); // pour connaitre la méthode

        /* Do the methods that defines the rest of the elements for wrinting the object */ 
        if($order instanceof Order && $method === "POST"){
            // MESSAGE FOR THE MAIL
            /*
            $formMessage = "";
            $orderTotal = 0;
            
            foreach($order->getOrderItems() as $orderItem){
                $itemPrice = 0;

                $formMessage .= `<tr><td style="width:100%"><p style="display:block;height:auto;padding:15px 20px;color:#0d0d0d;background-color:#f8f8f8;border-radius: 20px;"><u>`.$orderItem->getItemPizza()->getName()."</u>";
                $itemPrice += $orderItem->getItemPizza()->getPrice();

                if(!empty($orderItem->getSupIngredients())){
                    foreach($orderItem->getSupIngredients() as $ingredient){
                        $formMessage .= `+ `.$ingredient->getName().` `;
                        $itemPrice += $ingredient->getPrice();
                    }                    
                }else{
                    $formMessage .= "+ Aucun ingrédient supplémentaire ";
                }
                $formMessage .= `<i> = `.$itemPrice.` €</i><br></td></tr>`;
                $orderTotal += $itemPrice;
            }

            $formMessage .= `<tr><td><hr color="#f8f8f8"></td></tr>
            <tr><td style="width:100%"><p align="center" style="display:block;;height:auto;padding:15px 20px;color:#0d0d0d;background-color:#ffd86b;border-radius: 20px;">`;

            if($order->getIfDelivered()){
                $formMessage .= "La commande sera livrée chez vous pour : ";
                $orderTotal += 3;
            } else {
                $formMessage .= "La commande sera à retirer chez nous au : 52 Chaussée de Bruxelles, 7800 Ath";
            }
            $formMessage .= $order->getDate()->format('Y-m-d H:i') . "</p></td></tr>";

            $formMessage .= `
            <tr>
                <td style="width:100%">
                    <p align="center" style="display:block;;height:auto;padding:15px 20px;background-color:#751b13;border-radius: 20px;">TOTAL : `. $orderTotal .`€</p>
                </td>
            </tr>`;
            */
        // SEND EMAIL

        //VARs
            $lineScan = "\n";
            $boundary = "-----=".md5(rand());

            $user = $order->getCustomer();
            //$formName = $user->getFirstName()." ".$user->getLastName();
            $formName = "Adrien Defraene";
        
        //MY EMAIL ADDRESS
            //$formMail = $user->getEmail();
            $formMail = "adriendefraene@gmail.com";
            
        //SUBJECT OF THE MAIL
            //$mailSubject = " Commande de Pizzle's #". $order->getId() ." - ". $order->getDate()->format('Y-m-d H:i');
            $mailSubject = "Mail de test";
            
        //HEADER OF THE MAIL
            $header = "From: \"Pizzle's\"<hey@adriendefraene.be>".$lineScan;
            $header .= "Reply-to:\"".$formName."\"<".$user->getEmail().">".$lineScan;
            $header .= "MIME-Version: 1.0".$lineScan;
            $header .= "Content-Type: multipart/alternative;".$lineScan." boundary=\"".$boundary."\"".$lineScan;

        //DEFINING THE MESSAGE
        /*
            $message_html = `
                <html lang="en" xmlns="http://www.w3.org/1999/xhtml" xmlns:o="urn:schemas-microsoft-com:office:office">
                    <head>
                        <meta charset="UTF-8"><meta name="viewport" content="width=device-width,initial-scale=1"><meta name="x-apple-disable-message-reformatting">
                        <style>@import url('https://fonts.googleapis.com/css2?family=Days+One&family=Montserrat:ital,wght@0,400;0,700;1,400;1,700&display=swap');</style>
                    </head>
                    <body style="padding:0;margin:0">
                        <table role="presentation" style="width:100%;border-collapse:collapse;border:0;border-spacing:0;background:#fff;font-family:'Montserrat',sans-serif">
                            <tbody>
                                <tr>
                                    <td style="padding:0;" align="center">
                                        <table role="presentation" style="width:600px;border-collapse:collapse;border:1px solid #ccc;border-spacing:0;text-align:left;background:#fff;">
                                            <tbody>
                                                <tr>
                                                    <td style="padding:30px 0 40px 0;background:#f8f8f8;border-radius:0 0 25px 25px;box-shadow:0 5px 10px #d2d2d2" align="center">
                                                        <a href="https://pizzles.adriendefraene.be">
                                                            <img src="http://pizzles.adriendefraene.be/images/logos/PIZZLES_PLAIN.png" alt="Logo de Pizzles" style="height:auto;display:block;width:50%;">
                                                        </a>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td style="padding:30px;">
                                                        <p>Bonjour `. $user->getFirstName().` `.$user->getLastName() .`,</p>
                                                        <br>
                                                        <p>Voici votre reçu pour votre commande passée chez nous :</p>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td style="padding:0 20px">
                                                        <table style="padding:20px;background:#f84030;border-radius:25px;width:100%;height:auto;color:#f8f8f8;font-family:'Days One',sans-serif;box-shadow:0 0 10px #f85748">
                                                            <tbody>
                                                                <tr>
                                                                    <td align="center"  style="padding:10px 0 20px 0;">
                                                                        <p><strong>Commande #`. $order->getId() .` du `. $order->getDate()->format('d F Y') .`</strong></p>
                                                                    </td>
                                                                </tr>`
                                                                .$formMessage.
                                                            `</tbody>
                                                        </table>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td style="padding:30px;">
                                                        <p>Merci d'avoir pris votre commande chez nous !</p>
                                                        <br>
                                                        <p>En vous souhaitant un bon repas,</p>
                                                        <p>L'équipe de Pizzles</p>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td style="padding: 20px;background:#545454;color:#f8f8f8;" align="center">
                                                        <a href="https://pizzles.adriendefraene.be">
                                                            <img src="http://pizzles.adriendefraene.be/images/logos/PIZZLES_W.png" style="height:40px;width:auto" alt="Logo de Pizzles en blanc">
                                                        </a>
                                                        <br>
                                                        <br>
                                                        <a href="#" style="color:#f8f8f8;">www.pizzles-ath.be</a>
                                                        <hr>
                                                        <p>(c) EPSE-LADP WEBD EI 2021<br>Defraene Adrien</p>
                                                    </td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </body>
                </html>
            `;
*/
            $messageDeTest = "Oui, tu as bien reçu le mail!!!!";

        //DEFINING THE MESSAGE TO BE SENT
            //ADD MESSAGE IN HTML FORMAT
            $messageFinal = $lineScan."--".$boundary.$lineScan;
            $messageFinal .= "Content-Type: text/html; charset=\"ISO-8859-1\"".$lineScan;
            $messageFinal .= "Content-Transfer-Encoding: 8bit".$lineScan;
            $messageFinal .= $lineScan.$messageDeTest.$lineScan;
            //END
            $messageFinal .= $lineScan."--".$boundary."--".$lineScan;
            $messageFinal .= $lineScan."--".$boundary."--".$lineScan;

            mail($formMail, $mailSubject, $messageFinal, $header);
        }  
    } 
} 
?> 