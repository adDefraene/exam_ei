<?php

namespace App\DataFixtures;

use Faker\Factory;
use App\Entity\User;
use App\Entity\Ingredient;
use App\Entity\Order;
use App\Entity\OrderItem;
use App\Entity\Pizza;
use App\Entity\Review;
use App\Repository\PizzaRepository;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class AppFixtures extends Fixture
{
    // Password hash management
    private $encoder;

    public function __construct(UserPasswordEncoderInterface $encoder)
    {
        $this->encoder = $encoder;
    }

    public function load(ObjectManager $manager)
    {
        $faker = Factory::create('fr_FR');

    // Admin management
        $admin = new User();
        $admin->setFirstName("Adrien")
            ->setLastName("Defraene")
            ->setEmail("adriendefraene@gmail.com")
            ->setPassword($this->encoder->encodePassword($admin, "password"))
            ->setAddress("Grand Place 1")
            ->setCity("Ath")
            ->setRoles(['ROLE_ADMIN'])
        ;
        $manager->persist($admin);

    // Users management
        $users = []; // Array initialized for the orders
        $genders = ['male','femelle']; // For faker
        $cities = ["Ath","Arbre","Bouvignies","Ghislenghien","Gibecq","Houtaing","Irchonwelz","Isières","Lanquesaint","Ligne","Maffle","Mainvault","Meslin-L'Évèque","Moulbaix","Ormeignies","Ostiches","Rebaix","Villers-Notre-Dame",
        "Villers-Saint-Amand"];

        for($u=1; $u<=30; $u++){
            $user = new User;
            $gender = $faker->randomElement($genders);
            
            $user->setFirstName($faker->firstName($gender))
                ->setLastName($faker->lastName($gender))
                ->setEmail($faker->email())
                ->setPassword($this->encoder->encodePassword($user, "password"))
                ->setAddress($faker->streetName() ." ". $faker->buildingNumber())
                ->setCity($faker->randomElement($cities))
            ;

            $manager->persist($user);

            $users[] = $user;
        }

    // Ingredients management
        $ingredients = []; // Array initialized for the pizzas & supIngredients
        $ingredientsNames = $faker->words(30, false); // Array of the ingredients names

        foreach($ingredientsNames as $ingredientName){
            $ingredient = new Ingredient;
            $ingredient->setName($ingredientName)
                ->setPrice(rand(2,20)/10)
                ->setImage('https://picsum.photos/id/'.$faker->numberBetween(1,200).'/50/50');
            
            $manager->persist($ingredient);

            $ingredients[] = $ingredient;
        }

    // Pizzas management
        $pizzas = []; // Array initialized for the orders
        $pizza_promo = 0; // Counter of the promo pizzas

        for($p=1; $p<=14; $p++){
            $pizza = new Pizza;

        // Basic settings
            $pizza->setName($faker->sentence(rand(2,3), false))
                ->setDescription($faker->text(200))
                ->setPrice(($faker->numberBetween(800, 1800))/100)
                ->setImage('https://picsum.photos/id/'.$faker->numberBetween(1,200).'/300/200')
            ;

        // Adds the ingredients
            $pizza_ingredients = $faker->randomElements($ingredients, rand (1,5));
            foreach($pizza_ingredients as $pizza_ingredient){
                $pizza->addIngredient($pizza_ingredient);
            }

        // Defining the pizza type
            if($p === 1){
            // If first pizza => this is the Pizza of the month
                $pizza->setType("POTM");
            }else if($pizza_promo <= 2){
            // If the three next pizzas, pizzas in promo
                    $pizza->setType("PROMO");
                    $pizza_promo++;
            }else{
            // If not, classic pizza without promo
                    $pizza->setType("CLASSIC");
            }

            $manager->persist($pizza);

            $pizzas[] = $pizza;
        }

    // Orders management
        
        for($o=1; $o <= 99; $o++){
            $order = new Order;
            $order_chance = (rand()%2);

            $order->setState("DELIVERED")
                ->setCustomer($faker->randomElement($users))
                ->setDate($faker->dateTimeBetween('-1 months'))
                ->setIfDelivered($order_chance)
                ;

            // Adding orders items
            for($oi=1; $oi <= rand(1,4); $oi++){
                $orderItem = new OrderItem;
                $orderItem->setItemOrder($order)
                    ->setItemPizza($faker->randomElement($pizzas));
                
                // Adding or not supp ingredients
                    // If so, get the ingredients already not included
                    /*
                    $ingredients_not_in_pizza = array_diff($ingredients, $orderItem->getItemPizza()->getIngredientsInArray());

                    for($oig=1; $oig <= rand(0,3); $oig++){
                        // Get one
                        $supIngredient = $faker->randomElement($ingredients_not_in_pizza);
                        // Add it
                        $orderItem->addSupIngredient($supIngredient);
                        // Delete it from our array of ingredients to not put again
                        unset($ingredients_not_in_pizza[$supIngredient]);
                    }
                    */

                $manager->persist($orderItem);

                $order->addOrderItem($orderItem);
            }

            $order->setTotal();

            // Adding or not a review

            $review_chance = rand();
            if(($review_chance)%2 === 0){
                $review = new Review;
                $review->setReview($faker->text(rand(60,100)))
                    ->setStarsService(rand(3,10))
                    ->setStarsQuality(rand(3,10))
                    ->setStarsPunctuality(rand(3,10))
                ;

                $manager->persist($review);

                $order->setReview($review);
            }

            $manager->persist($order);

        }


        $manager->flush();
    }
}
