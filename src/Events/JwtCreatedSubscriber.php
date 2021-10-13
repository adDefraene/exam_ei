<?php

namespace App\Events;

use Lexik\Bundle\JWTAuthenticationBundle\Event\JWTCreatedEvent;

class JwtCreatedSubscriber
{
    public function updateJwtData(JWTCreatedEvent $event){
        // récupérer l'utilsateur (pour avoir son firstName et lastName)
        $user = $event->getUser();// permet de récupèrer l'utilisateur
        
        // enrichir les data pour qu'elles contiennent d’autres données
        $data = $event->getData(); // récupère un tableau qui contient les données de base sur l'utilisateur dans le JWT
        $data['firstName'] = $user->getFirstName();
        $data['lastName'] = $user->getLastName();
        $data['id'] = $user->getId();
        
        $event->setData($data); // on repasse le tableau data une fois qu'il est modifié
    }
}