<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\OneToOne;
use App\Repository\OrderRepository;
use Doctrine\ORM\Mapping\OneToMany;
use Doctrine\ORM\PersistentCollection;
use ApiPlatform\Core\Annotation\ApiFilter;
use ApiPlatform\Core\Action\NotFoundAction;
use Doctrine\Common\Collections\Collection;
use ApiPlatform\Core\Annotation\ApiResource;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Serializer\Annotation\Groups;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\OrderFilter;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\SearchFilter;

/**
 * @ORM\Entity(repositoryClass=OrderRepository::class)
 * @ORM\Table(name="`order`")
 * 
 * @ApiResource(
 *      attributes={
 *          "order"={"id":"desc"}
 *      },
 *      collectionOperations={"GET", "POST"},
 *      itemOperations={"GET"},
 *      normalizationContext={
 *          "groups"={"orders_read"}
 *      }
 * )
 * @ApiFilter(
 *      SearchFilter::class,
 *      properties={"state"}
 * )
 * @ApiFilter(
 *      OrderFilter::class,
 *      properties={"date"}
 * )
 */
class Order
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     * @Groups({"orders_read"})
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     * @Groups({"orders_read"})
     */
    private $state;

    /**
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="orders")
     * @ORM\JoinColumn(nullable=false)
     * @Groups({"reviews_read"})
     */
    private $customer;

    /**
     * @ORM\OneToOne(targetEntity=Review::class, mappedBy="reviewedOrder", cascade={"persist", "remove"})
     * @Groups({"orders_read"}))
     */
    private $review;

    /**
     * @ORM\OneToMany(targetEntity=OrderItem::class, mappedBy="itemOrder",  cascade={"persist", "remove"})
     * @Groups({"orders_read"})
     */
    private $orderItems;

    /**
     * @ORM\Column(type="datetime")
     * @Groups({"orders_read"})
     */
    private $date;

    /**
     * @ORM\Column(type="boolean")
     * @Groups({"orders_read"})
     */
    private $ifDelivered;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    private $orderItemsJson;

    /**
     * @ORM\Column(type="decimal", precision=5, scale=2)
     * @Groups({"orders_read"})
     */
    private $total;

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

    public function setDate($date): self
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

    /**
     * Gets the order in a JSON string
     *
     * @return string|null
     */
    public function getOrderItemsJson(): ?string
    {
        return $this->orderItemsJson;
    }

    /**
     */
    public function setOrderItemsJson($orderItemsJson)
    {
        $this->orderItemsJson = $orderItemsJson;
    }

    /**
     * Returns the total of an order
     *
     * @return float
     */
    public function getTotal()
    {
        return $this->total;
    }

    /**
     * Sets automatically the total from the ordered elements
     *
     * @return float
     */
    public function setTotal()
    {
        $total = 0;

        // For each pizza ordered
        foreach($this->getOrderItems() as $orderItem){
            // If is in promo : -25%
            if($orderItem->getItemPizza()->getType() === "PROMO"){
                $total += floatval($orderItem->getItemPizza()->getPrice()) * 0.75;
            } else {
                $total += floatval($orderItem->getItemPizza()->getPrice());
            }
            //For each sup ingredient
            foreach($orderItem->getSupIngredients() as $supIngredient){
                $total += floatval($supIngredient->getPrice());
            }
        }

        if($this->getIfDelivered()){
            $total += 3;
        }
         
        // Sets it
        $this->total = $total;
    }

}
