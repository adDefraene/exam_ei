<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use App\Repository\ReviewRepository;
use ApiPlatform\Core\Annotation\ApiResource;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass=ReviewRepository::class)
 * @ApiResource(
 *      collectionOperations={"GET", "POST"},
 *      itemOperations={"GET"},
 *      normalizationContext={
 *          "groups"={"orders_read"}
 *      },
 *      denormalizationContext={"disable_type_enforcement"=true} ,
 *      attributes={
 *          "order"={"reviewedOrder.date":"desc"},
 *          "pagination_enabled"=true,
 *          "pagination_items_per_page"=6
 *      }
 * )
 */
class Review
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\OneToOne(targetEntity=Order::class, inversedBy="review", cascade={"detach"})
     */
    private $reviewedOrder;

    /**
     * @ORM\Column(type="text")
     * @Assert\NotBlank(message="Le texte de l'évaluation est obligatoire")
     * @Assert\Length(max=120, maxMessage="Évalutation trop longue")
     * @Groups({"orders_read"})
     */
    private $review;

    /**
     * @ORM\Column(type="float")
     * @Assert\NotBlank(message="La note de qualité est obligatoire")
     * @Groups({"orders_read"})
     */
    private $starsQuality;

    /**
     * @ORM\Column(type="float")
     * @Assert\NotBlank(message="La note du service est obligatoire")
     * @Groups({"orders_read"})
     */
    private $starsService;

    /**
     * @ORM\Column(type="float")
     * @Assert\NotBlank(message="La note de la ponctualité est obligatoire")
     * @Groups({"orders_read"})
     */
    private $starsPunctuality;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getReviewedOrder(): ?Order
    {
        return $this->reviewedOrder;
    }

    public function setReviewedOrder(?Order $reviewedOrder): self
    {
        $this->reviewedOrder = $reviewedOrder;

        return $this;
    }

    public function getReview(): ?string
    {
        return $this->review;
    }

    public function setReview(string $review): self
    {
        $this->review = $review;

        return $this;
    }

    public function getStarsQuality(): ?float
    {
        return $this->starsQuality;
    }

    public function setStarsQuality($starsQuality): self
    {
        $this->starsQuality = $starsQuality;

        return $this;
    }

    public function getStarsService(): ?float
    {
        return $this->starsService;
    }

    public function setStarsService($starsService): self
    {
        $this->starsService = $starsService;

        return $this;
    }

    public function getStarsPunctuality(): ?float
    {
        return $this->starsPunctuality;
    }

    public function setStarsPunctuality($starsPunctuality): self
    {
        $this->starsPunctuality = $starsPunctuality;

        return $this;
    }

    /**
     * Gets the average notes of the notes
     * @Groups({"orders_read"})
     *
     * @return float
     */
    public function getAverageRating(){
        return floor(($this->starsService + $this->starsQuality + $this->starsPunctuality)/3);
    }
}
