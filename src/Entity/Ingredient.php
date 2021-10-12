<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use App\Repository\IngredientRepository;
use ApiPlatform\Core\Annotation\ApiFilter;
use ApiPlatform\Core\Action\NotFoundAction;
use ApiPlatform\Core\Annotation\ApiResource;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\SearchFilter;

/**
 * @ORM\Entity(repositoryClass=IngredientRepository::class)
 * 
 * @ApiResource(
 *      collectionOperations={"GET"},
 *      itemOperations={"GET"},
 *      normalizationContext={
 *          "groups"={"ingredients_read", "pizzas_read"}
 *      },
 *      attributes={
 *          "order"={"name":"asc"}
 *      }
 * )
 * @ApiFilter(
 *      SearchFilter::class,
 *      properties={"type"}
 * )
 */
class Ingredient
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     * @Groups({"ingredients_read","pizzas_read","orders_read"})
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank(message="Vous devez renseigner un nom")
     * @Groups({"ingredients_read", "pizzas_read", "orders_read"})
     */
    private $name;

    /**
     * @ORM\Column(type="decimal", precision=3, scale=2)
     * @Groups({"ingredients_read"})
     */
    private $price;

    /**
     * @ORM\Column(type="string", length=255)
     * @Groups({"ingredients_read", "pizzas_read"})
     */
    private $image;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getPrice(): ?string
    {
        return $this->price;
    }

    public function setPrice(string $price): self
    {
        $this->price = $price;

        return $this;
    }

    public function getImage(): ?string
    {
        return $this->image;
    }

    public function setImage(string $image): self
    {
        $this->image = $image;

        return $this;
    }
}
