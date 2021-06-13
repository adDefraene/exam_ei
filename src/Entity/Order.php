<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\OneToOne;
use App\Repository\OrderRepository;
use Doctrine\ORM\Mapping\OneToMany;
use Doctrine\ORM\PersistentCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * @ORM\Entity(repositoryClass=OrderRepository::class)
 * @ORM\Table(name="`order`")
 */
class Order
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $state;

    /**
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="orders")
     * @ORM\JoinColumn(nullable=false)
     */
    private $customer;

    /**
     * @ORM\OneToOne(targetEntity=Review::class, mappedBy="reviewedOrder", cascade={"persist", "remove"})
     */
    private $review;

    /**
     * @ORM\OneToMany(targetEntity=OrderItem::class, mappedBy="itemOrder",  cascade={"persist", "remove"})
     */
    private $orderItems;

    /**
     * @ORM\Column(type="datetime")
     */
    private $date;

    /**
     * @ORM\Column(type="boolean")
     */
    private $ifDelivered;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    private $orderItemsJson;

    public function __construct()
    {
        $this->orderItems = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getState(): ?string
    {
        return $this->state;
    }

    public function setState(string $state): self
    {
        $this->state = $state;

        return $this;
    }

    public function getCustomer(): ?User
    {
        return $this->customer;
    }

    public function setCustomer(?User $customer): self
    {
        $this->customer = $customer;

        return $this;
    }

    public function getReview(): ?Review
    {
        return $this->review;
    }

    public function setReview(?Review $review): self
    {
        // unset the owning side of the relation if necessary
        if ($review === null && $this->review !== null) {
            $this->review->setReviewedOrder(null);
        }

        // set the owning side of the relation if necessary
        if ($review !== null && $review->getReviewedOrder() !== $this) {
            $review->setReviewedOrder($this);
        }

        $this->review = $review;

        return $this;
    }

    /**
     * @return Collection|OrderItem[]
     */
    public function getOrderItems(): Collection
    {
        return $this->orderItems;
    }

    public function addOrderItem(OrderItem $orderItem): self
    {
        if (!$this->orderItems->contains($orderItem)) {
            $this->orderItems[] = $orderItem;
            $orderItem->setItemOrder($this);
        }

        return $this;
    }

    public function removeOrderItem(OrderItem $orderItem): self
    {
        if ($this->orderItems->removeElement($orderItem)) {
            // set the owning side to null (unless already changed)
            if ($orderItem->getItemOrder() === $this) {
                $orderItem->setItemOrder(null);
            }
        }

        return $this;
    }

    public function getDate(): ?\DateTimeInterface
    {
        return $this->date;
    }

    public function setDate(\DateTimeInterface $date): self
    {
        $this->date = $date;

        return $this;
    }

    public function getIfDelivered(): ?bool
    {
        return $this->ifDelivered;
    }

    public function setIfDelivered(bool $ifDelivered): self
    {
        $this->ifDelivered = $ifDelivered;

        return $this;
    }

    public function getOrderItemsJson(): ?string
    {
        return $this->orderItemsJson;
    }

    public function setOrderItemsJson(?string $orderItemsJson): self
    {
        $this->orderItemsJson = $orderItemsJson;

        return $this;
    }

    public function getTotal(){
        $total = 0;

        foreach($this->getOrderItems() as $orderItem){
            if($orderItem->getItemPizza()->getType() === "PROMO"){
                $total .= ($orderItem->getItemPizza()->getPrice() * 0.75);
            } else {
                $total .= ($orderItem->getItemPizza()->getPrice());
            }

            foreach($orderItem->getSupIngredients() as $supIngredient){
                $total .= $supIngredient->getPrice();
            }
        }

        return $total;
    }

}
