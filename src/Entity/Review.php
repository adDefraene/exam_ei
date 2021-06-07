<?php

namespace App\Entity;

use App\Repository\ReviewRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=ReviewRepository::class)
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
     * @ORM\OneToOne(targetEntity=Order::class, inversedBy="review", cascade={"persist", "remove"})
     */
    private $reviewedOrder;

    /**
     * @ORM\Column(type="text")
     */
    private $review;

    /**
     * @ORM\Column(type="float")
     */
    private $starsQuality;

    /**
     * @ORM\Column(type="float")
     */
    private $starsService;

    /**
     * @ORM\Column(type="float")
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

    public function setStarsQuality(float $starsQuality): self
    {
        $this->starsQuality = $starsQuality;

        return $this;
    }

    public function getStarsService(): ?float
    {
        return $this->starsService;
    }

    public function setStarsService(float $starsService): self
    {
        $this->starsService = $starsService;

        return $this;
    }

    public function getStarsPunctuality(): ?float
    {
        return $this->starsPunctuality;
    }

    public function setStarsPunctuality(float $starsPunctuality): self
    {
        $this->starsPunctuality = $starsPunctuality;

        return $this;
    }
}
