<?php

namespace App\Entity;

use App\Repository\UserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * @ORM\Entity(repositoryClass=UserRepository::class)
 */
class User
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     * @Groups({"gardensWithRelations"})
     * @Groups({"users"})
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=64)
     * @Assert\NotBlank
     * @Assert\Length(max=64)
     * @Groups({"gardensWithRelations"})
     * @Groups({"users"})
     */
    private $username;

    /**
     * @ORM\Column(type="string", length=64)
     * @Assert\NotBlank
     * @Assert\Length(max=64)
     * @Groups({"users"})
     */
    private $password;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank
     * @Assert\Email
     * @Assert\Length(max=255)
     * @Groups({"gardensWithRelations"})
     * @Groups({"users"})
     */
    private $email;

    /**
     * @ORM\Column(type="string", length=64, nullable=true)
     * @Assert\NotBlank
     * @Assert\Length(max=64)
     * @Groups({"gardensWithRelations"})
     * @Groups({"users"})
     */
    private $phone;

    /**
     * @ORM\Column(type="string", length=128)
     * @Assert\NotBlank
     * @Assert\Length(max=128)
     * @Groups({"users"})
     */
    private $role;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Assert\NotBlank
     * @Assert\Url
     * @Groups({"gardensWithRelations"})
     * @Groups({"users"})
     */
    private $avatar;

    /**
     * @ORM\Column(type="datetime_immutable")
     * @Groups({"users"})
     */
    private $createdAt;

    /**
     * @ORM\Column(type="datetime_immutable", nullable=true)
     * @Groups({"users"})
     */
    private $updatedAt;

    /**
     * @ORM\OneToMany(targetEntity=Garden::class, mappedBy="user")
     * @Groups({"userWithRelations"})
     */
    private $gardens;

    /**
     * @ORM\OneToMany(targetEntity=Favorite::class, mappedBy="user", orphanRemoval=true)
     * @Groups({"userWithRelations"})
     */
    private $favorites;

    public function __construct()
    {
        $this->gardens = new ArrayCollection();
        $this->favorites = new ArrayCollection();
        $this->createdAt = new \DateTimeImmutable();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUsername(): ?string
    {
        return $this->username;
    }

    public function setUsername(string $username): self
    {
        $this->username = $username;

        return $this;
    }

    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }

    public function getPhone(): ?string
    {
        return $this->phone;
    }

    public function setPhone(?string $phone): self
    {
        $this->phone = $phone;

        return $this;
    }

    public function getRole(): ?string
    {
        return $this->role;
    }

    public function setRole(string $role): self
    {
        $this->role = $role;

        return $this;
    }

    public function getAvatar(): ?string
    {
        return $this->avatar;
    }

    public function setAvatar(?string $avatar): self
    {
        $this->avatar = $avatar;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeImmutable $created_at): self
    {
        $this->createdAt = $created_at;

        return $this;
    }

    public function getUpdatedAt(): ?\DateTimeImmutable
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(?\DateTimeImmutable $updated_at): self
    {
        $this->updatedAt = $updated_at;

        return $this;
    }

    /**
     * @return Collection<int, Garden>
     */
    public function getGardens(): Collection
    {
        return $this->gardens;
    }

    public function addGarden(Garden $garden): self
    {
        if (!$this->gardens->contains($garden)) {
            $this->gardens[] = $garden;
            $garden->setUser($this);
        }

        return $this;
    }

    public function removeGarden(Garden $garden): self
    {
        if ($this->gardens->removeElement($garden)) {
            // set the owning side to null (unless already changed)
            if ($garden->getUser() === $this) {
                $garden->setUser(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Favorite>
     */
    public function getFavorites(): Collection
    {
        return $this->favorites;
    }

    public function addFavorite(Favorite $favorite): self
    {
        if (!$this->favorites->contains($favorite)) {
            $this->favorites[] = $favorite;
            $favorite->setUser($this);
        }

        return $this;
    }

    public function removeFavorite(Favorite $favorite): self
    {
        if ($this->favorites->removeElement($favorite)) {
            // set the owning side to null (unless already changed)
            if ($favorite->getUser() === $this) {
                $favorite->setUser(null);
            }
        }

        return $this;
    }
}