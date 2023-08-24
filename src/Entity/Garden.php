<?php

namespace App\Entity;

use App\Repository\GardenRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * @ORM\Entity(repositoryClass=GardenRepository::class)
 */
class Garden
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     * @Groups({"gardens"})
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=128)
     * @Assert\NotBlank
     * @Assert\Length(max=128)
     * @Groups({"gardens"})
     */
    private $title;

    /**
     * @ORM\Column(type="string", length=1000)
     * @Assert\NotBlank
     * @Assert\Length(max=1000)
     * @Groups({"gardens"})
     */
    private $description;

    /**
     * @ORM\Column(type="string", length=240)
     * @Assert\NotBlank
     * @Assert\Length(max=240)
     */
    private $address;

    /**
     * @ORM\Column(type="integer")
     * @Assert\NotBlank
     */
    private $postalCode;

    /**
     * @ORM\Column(type="string", length=128)
     * @Assert\NotBlank
     * @Assert\Length(max=128)
     */
    private $city;

    /**
     * @ORM\Column(type="boolean")
     * @Assert\NotBlank
     */
    private $water;

    /**
     * @ORM\Column(type="boolean")
     * @Assert\NotBlank
     */
    private $tool;

    /**
     * @ORM\Column(type="boolean")
     * @Assert\NotBlank
     */
    private $shed;

    /**
     * @ORM\Column(type="boolean")
     * @Assert\NotBlank
     */
    private $cultivation;

    /**
     * @ORM\Column(type="integer")
     * @Assert\NotBlank
     */
    private $surface;

    /**
     * @ORM\Column(type="boolean")
     * @Assert\NotBlank
     */
    private $phoneAccess;

    /**
     * @ORM\Column(type="datetime_immutable")
     */
    private $createdAt;

    /**
     * @ORM\Column(type="datetime_immutable", nullable=true)
     */
    private $updatedAt;

    /**
     * @ORM\Column(type="string", length=128)
     * @Assert\NotBlank
     * @Assert\Length(max=128)
     */
    private $state;

    /**
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="gardens")
     * @ORM\JoinColumn(nullable=false)
     */
    private $user;

    /**
     * @ORM\OneToMany(targetEntity=Picture::class, mappedBy="garden", orphanRemoval=true)
     */
    private $pictures;

    /**
     * @ORM\OneToMany(targetEntity=Favorite::class, mappedBy="Garden", orphanRemoval=true)
     */
    private $favorites;

    public function __construct()
    {
        $this->pictures = new ArrayCollection();
        $this->favorites = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): self
    {
        $this->title = $title;

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

    public function getAddress(): ?string
    {
        return $this->address;
    }

    public function setAddress(string $address): self
    {
        $this->address = $address;

        return $this;
    }

    public function getPostalCode(): ?string
    {
        return $this->postalCode;
    }

    public function setPostalCode(string $postalCode): self
    {
        $this->postalCode = $postalCode;

        return $this;
    }

    public function getCity(): ?string
    {
        return $this->city;
    }

    public function setCity(string $city): self
    {
        $this->city = $city;

        return $this;
    }

    public function isWater(): ?bool
    {
        return $this->water;
    }

    public function setWater(bool $water): self
    {
        $this->water = $water;

        return $this;
    }

    public function isTool(): ?bool
    {
        return $this->tool;
    }

    public function setTool(bool $tool): self
    {
        $this->tool = $tool;

        return $this;
    }

    public function isShed(): ?bool
    {
        return $this->shed;
    }

    public function setShed(bool $shed): self
    {
        $this->shed = $shed;

        return $this;
    }

    public function isCultivation(): ?bool
    {
        return $this->cultivation;
    }

    public function setCultivation(bool $cultivation): self
    {
        $this->cultivation = $cultivation;

        return $this;
    }

    public function getSurface(): ?int
    {
        return $this->surface;
    }

    public function setSurface(int $surface): self
    {
        $this->surface = $surface;

        return $this;
    }

    public function isPhoneAccess(): ?bool
    {
        return $this->phoneAccess;
    }

    public function setPhoneAccess(bool $phoneAccess): self
    {
        $this->phoneAccess = $phoneAccess;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeImmutable $createdAt): self
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getUpdatedAt(): ?\DateTimeImmutable
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(?\DateTimeImmutable $updatedAt): self
    {
        $this->updatedAt = $updatedAt;

        return $this;
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

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): self
    {
        $this->user = $user;

        return $this;
    }

    /**
     * @return Collection<int, Picture>
     */
    public function getPictures(): Collection
    {
        return $this->pictures;
    }

    public function addPicture(Picture $picture): self
    {
        if (!$this->pictures->contains($picture)) {
            $this->pictures[] = $picture;
            $picture->setGarden($this);
        }

        return $this;
    }

    public function removePicture(Picture $picture): self
    {
        if ($this->pictures->removeElement($picture)) {
            // set the owning side to null (unless already changed)
            if ($picture->getGarden() === $this) {
                $picture->setGarden(null);
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
            $favorite->setGarden($this);
        }

        return $this;
    }

    public function removeFavorite(Favorite $favorite): self
    {
        if ($this->favorites->removeElement($favorite)) {
            // set the owning side to null (unless already changed)
            if ($favorite->getGarden() === $this) {
                $favorite->setGarden(null);
            }
        }

        return $this;
    }
}
