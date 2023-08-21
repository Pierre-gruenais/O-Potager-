<?php

namespace App\Entity;

use App\Repository\GardenRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=GardenRepository::class)
 */
class Garden
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=128)
     */
    private $title;

    /**
     * @ORM\Column(type="string", length=1000)
     */
    private $description;

    /**
     * @ORM\Column(type="string", length=240)
     */
    private $address;

    /**
     * @ORM\Column(type="integer")
     */
    private $postalCode;

    /**
     * @ORM\Column(type="string", length=128)
     */
    private $city;

    /**
     * @ORM\Column(type="boolean")
     */
    private $water;

    /**
     * @ORM\Column(type="boolean")
     */
    private $tool;

    /**
     * @ORM\Column(type="boolean")
     */
    private $shed;

    /**
     * @ORM\Column(type="boolean")
     */
    private $cultivation;

    /**
     * @ORM\Column(type="integer")
     */
    private $surface;

    /**
     * @ORM\Column(type="boolean")
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

    public function getPostalCode(): ?int
    {
        return $this->postalCode;
    }

    public function setPostalCode(int $postalCode): self
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
}