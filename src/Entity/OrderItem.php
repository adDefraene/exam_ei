<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\PersistentCollection;
use App\Repository\OrderItemRepository;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * @ORM\Entity(repositoryClass=OrderItemRepository::class)
 */
class OrderItem
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity=Order::class, inversedBy="orderItems")
     */
    private $itemOrder;

    /**
     * @ORM\ManyToOne(targetEntity=Pizza::class, cascade={"detach"})
     * @Groups({"orders_read"})
     */
    private $itemPizza;

    /**
     * @ORM\ManyToMany(targetEntity=Ingredient::class, cascade={"detach"})
     * @Groups({"orders_read"})
     */
    private $supIngredients;

    public function __construct()
    {
        $this->supIngredients = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getItemOrder(): ?Order
    {
        return $this->itemOrder;
    }

    public function setItemOrder(?Order $itemOrder): self
    {
        $this->itemOrder = $itemOrder;

        return $this;
    }

    public function getItemPizza(): ?Pizza
    {
        return $this->itemPizza;
    }

    public function setItemPizza(?Pizza $itemPizza): self
    {
        $this->itemPizza = $itemPizza;

        return $this;
    }

    /**
     * @return Collection|Ingredient[]
     */
    public function getSupIngredients(): Collection
    {
        return $this->supIngredients;
    }

    public function addSupIngredient(Ingredient $supIngredient): self
    {
        if (!$this->supIngredients->contains($supIngredient)) {
            $this->supIngredients[] = $supIngredient;
        }

        return $this;
    }

    public function removeSupIngredient(Ingredient $supIngredient): self
    {
        $this->supIngredients->removeElement($supIngredient);

        return $this;
    }
}
