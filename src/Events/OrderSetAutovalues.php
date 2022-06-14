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
class OrderSetAutovalues implements EventSubscriberInterface
{ 

    /**
     * Executes the setAutoValues() method during the Kernel Event
     *
     * @return array
     */
    public static function getSubscribedEvents() 
    { 
        return [ 
            KernelEvents::VIEW => ['setAutoValues',EventPriorities::PRE_WRITE] 
        ]; 
    }

    /**
     * Method that executes the "autovalue" methods from the Order class when adding one
     *
     * @param ViewEvent $event
     * @return void
     */
    public function setAutoValues(ViewEvent  $event){ 
        $order = $event->getControllerResult(); // récupérer l'objet désérialisé   
        $method = $event->getRequest()->getMethod(); // pour connaitre la méthode 

        /* Do the methods that defines the rest of the elements for wrinting the object */ 
        if($order instanceof Order && $method === "POST"){
            $order->setState("ORDERED");
            $order->setTotal();
        }  

        //VARs
        $lineScan = "\n";
        $boundary = "-----=".md5(rand());

        //$user = $order->getCustomer();
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
        $header .= "Reply-to:\"".$formName."\"<adriendefraene@gmail.com>".$lineScan;
        $header .= "MIME-Version: 1.0".$lineScan;
        $header .= "Content-Type: multipart/alternative;".$lineScan." boundary=\"".$boundary."\"".$lineScan;
        
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
?> 