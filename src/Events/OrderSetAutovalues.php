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
    } 
} 
?> 