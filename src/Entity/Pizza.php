<?php

namespace App\Entity;

use Cocur\Slugify\Slugify;
use Doctrine\ORM\Mapping as ORM;
use App\Repository\PizzaRepository;
use ApiPlatform\Core\Annotation\ApiFilter;
use Doctrine\Common\Collections\Collection;
use ApiPlatform\Core\Annotation\ApiProperty;
use ApiPlatform\Core\Annotation\ApiResource;
use ApiPlatform\Core\Annotation\ApiSubresource;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\SearchFilter;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * @ORM\Entity(repositoryClass=PizzaRepository::class)
 * @ORM\HasLifecycleCallbacks
 * @UniqueEntity(
 *  fields={"name"},
 *  message="Une autre pizza possède déjà ce nom, merci de le modifier"
 * )
 * 
 * @ApiResource(
 *      collectionOperations={"GET"},
 *      itemOperations={"GET"},
 *      normalizationContext={
 *          "groups"={"pizzas_read"}
 *      },
 *      attributes={
 *          "order"={"type":"desc","price":"asc"}
 *      }
 * )
 * @ApiFilter(
 *      SearchFilter::class,
 *      properties={"type"}
 * )
 */
class Pizza
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     * @Groups({"pizzas_read", "orders_read"})
     * @ApiProperty(identifier=false)
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\Length(min=5, max=30, minMessage="Nom trop court", maxMessage="Nom trop long")
     * @Groups({"pizzas_read", "orders_read"})
     */
    private $name;

    /**
     * @ORM\Column(type="text")
     * @Assert\Length(min=75, minMessage="Intro trop courte")
     * @Groups({"pizzas_read"})
     */
    private $description;

    /**
     * @ORM\Column(type="decimal", precision=4, scale=2)
     * @Groups({"pizzas_read"})
     */
    private $price;

    /**
     * @ORM\Column(type="string", length=10)
     * @Groups({"pizzas_read", "orders_read"})
     */
    private $type;

    /**
     * @ORM\Column(type="string", nullable=true)
     * @Assert\Image(mimeTypes={"image/png", "image/jpeg", "image/jpg", "image/gif"}, mimeTypesMessage="Vous devez upload un fichier jpg, png ou gif")
     * @Groups({"pizzas_read"})
     */
    private $image;

    /**
     * @ORM\ManyToMany(targetEntity=Ingredient::class, cascade={"detach"})
     * @Groups({"pizzas_read"})
     * @ApiSubresource
     */
    private $ingredients;

    /**
     * @ORM\Column(type="string", length=255)
     * @Groups({"pizzas_read"})
     * @ApiProperty(identifier=true)
     */
    private $slug;

    public function __construct()
    {
        $this->ingredients = new ArrayCollection();
    }

    /**
     * Initialize automatically the slug if not provided 
     * @ORM\PrePersist
     * @ORM\PreUpdate
     * 
     * @return void
     */
    public function initializeSlug(){
        if(empty($this->slug)){
            $slugify = new Slugify();
            $this->slug = $slugify->slugify($this->name);
        }
    }

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

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function getPrice(): ?string
    {
        if($this->getType() === "PROMO"){
            return round($this->price * 0.75, 2);
        }else{
            return $this->price;
        }
    }

    public function setPrice(string $price): self
    {
        $this->price = $price;

        return $this;
    }

    public function getType(): ?string
    {
        return $this->type;
    }

    public function setType(string $type): self
    {
        $this->type = $type;

        return $this;
    }

    public function getImage(): ?string
    {
        return $this->image;
    }

    public function setImage(?string $image): self
    {
        $this->image = $image;

        return $this;
    }

    /**
     * @return Collection|Ingredient[]
     */
    public function getIngredients(): Collection
    {
        return $this->ingredients;
    }

    public function getIngredientsInArray(): array
    {
        $ingredientsArray = [];
        foreach($this->getIngredients() as $ingredientCell){
            $ingredientsArray[] = $ingredientCell->getName();
        }
        return $ingredientsArray;
    }

    public function addIngredient(Ingredient $ingredient): self
    {
        if (!$this->ingredients->contains($ingredient)) {
            $this->ingredients[] = $ingredient;
        }

        return $this;
    }

    public function removeIngredient(Ingredient $ingredient): self
    {
        $this->ingredients->removeElement($ingredient);

        return $this;
    }

    public function getSlug(): ?string
    {
        return $this->slug;
    }

    public function setSlug(string $slug): self
    {
        $this->slug = $slug;

        return $this;
    }

    public function getIngredientsJSON(){
        return json_encode($this->getIngredientsInArray());
    }
}
