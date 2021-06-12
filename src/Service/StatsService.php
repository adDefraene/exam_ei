<?php

namespace App\Service;

use Doctrine\ORM\EntityManagerInterface;

class StatsService{
    private $manager;

    public function __construct(EntityManagerInterface $manager)
    {
        $this->manager = $manager;
    }

    public function getUsersCount(){
        return $this->manager->createQuery('SELECT COUNT(u) FROM App\Entity\User u')->getSingleScalarResult();
    }

    public function getPizzaCount()
    {
        return $this->manager->createQuery('SELECT COUNT(p) FROM App\Entity\Pizza p')->getSingleScalarResult();
    }

    public function getIngredientCount()
    {
        return $this->manager->createQuery('SELECT COUNT(i) FROM App\Entity\Ingredient i')->getSingleScalarResult();
    }

    public function getOrderCount()
    {
        return $this->manager->createQuery('SELECT COUNT(o) FROM App\Entity\Order o')->getSingleScalarResult();
    }

    public function getReviewsCount()
    {
        return $this->manager->createQuery('SELECT COUNT(r) FROM App\Entity\Review r')->getSingleScalarResult();
    }
/*
    public function getReviewsStats($direction)
    {
        return $this->manager->createQuery(
            'SELECT (r.starsQuality + r.starsService + r.starsPunctuality) as note, r.review, c.firstName, c.lastName
            FROM App\Entity\Review r
            JOIN r.reviewedOrder o
            JOIN o.customer c
            ORDER BY note '. $direction
        )
        ->setMaxResults(5)
        ->getResult();
    }
*/
}