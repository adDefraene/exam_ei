<?php

namespace App\Entity;
use Doctrine\ORM\Mapping as ORM;
use App\Repository\PasswordUpdateRepository;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass=PasswordUpdateRepository::class)
 */
class PasswordUpdate
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @Assert\NotBlank(message="Vous devez renseigner votre ancien mot de passe")
     */
    private $oldPassword;

    /**
     * @Assert\NotBlank(message="Vous devez renseigner un nouveau mot de passe")
     */
    private $newPassword;
    
    public function getId(): ?int
    {
        return $this->id;
    }

    public function getOldPassword(): ?string
    {
        return $this->oldPassword;
    }

    public function setOldPassword(string $oldPassword): self
    {
        $this->oldPassword = $oldPassword;

        return $this;
    }

    public function getNewPassword(): ?string
    {
        return $this->newPassword;
    }

    public function setNewPassword(string $newPassword): self
    {
        $this->newPassword = $newPassword;

        return $this;
    }
}
