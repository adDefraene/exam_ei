<?php 
namespace App\Events; 
use App\Entity\User; 
use Symfony\Component\HttpKernel\KernelEvents; 
use Symfony\Component\HttpKernel\Event\ViewEvent; 
use ApiPlatform\Core\EventListener\EventPriorities;
use App\Repository\UserRepository;
use Exception;
use Symfony\Component\EventDispatcher\EventSubscriberInterface; 
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

/**
 * 
 */
class PasswordEncoderSubscriber implements EventSubscriberInterface
{ 

    
    /**
     * @var UserPasswordEncoderInterface
     */
    private $encoder; 

    /**
     * @var UserRepository
     */
    private $userRepo;

    public function __construct(UserPasswordEncoderInterface $encoder, UserRepository $userRepo){ 
        $this->encoder = $encoder;
        $this->userRepo = $userRepo; 
    }

    public static function getSubscribedEvents() 
    { 
        return [ 
            KernelEvents::VIEW => ['encodePassword',EventPriorities::PRE_WRITE] 
        ]; 
    }
    
    public function encodePassword(ViewEvent  $event){
        $user = $event->getControllerResult(); // Get deserialized object
        $method = $event->getRequest()->getMethod(); // Get method type

        // Verify when a User is sent via POST
        if($user instanceof User && ($method === "POST")){
            // Hash its password
            if($user->getPassword() !== null){
                $hash = $this->encoder->encodePassword($user, $user->getPassword());
                $user->setPassword($hash);
            }
        }

        // Verify when a User is modified via PUT
        if($user instanceof User && ($method === "PUT")){
            if($user->oldPassword !== null && $user->newPassword !== null ){
                $currentUser = $this->userRepo->findOneById($user->getId());
                if(password_verify($user->oldPassword, $currentUser->getPassword())){
                    $hash = $this->encoder->encodePassword($user, $user->newPassword);
                    $user->setPassword($hash);
                }else{
                    throw new Exception("L'ancien mot de passe ne correspond pas !");
                }
            }
        }
    } 
} 
?> 