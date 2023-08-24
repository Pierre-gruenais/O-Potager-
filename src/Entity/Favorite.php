<?php

namespace App\Entity;

use App\Repository\FavoriteRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * @ORM\Entity(repositoryClass=FavoriteRepository::class)
 */
class Favorite
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     * @Groups({"userWithRelations"})
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity=Garden::class, inversedBy="favorites")
     * @ORM\JoinColumn(nullable=false)
     * @Groups({"userWithRelations"})
     */
    private $garden;

    /**
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="favorites")
     * @ORM\JoinColumn(nullable=false)
     */
    private $user;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getGarden(): ?Garden
    {
        return $this->garden;
    }

    public function setGarden(?Garden $Garden): self
    {
        $this->garden = $Garden;

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
}