<?php

namespace App\Entity;

use App\Repository\TeamVilainRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: TeamVilainRepository::class)]
class TeamVilain
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 50, unique: true)]
    private ?string $name = null;

    #[ORM\Column(length: 100, nullable: true)]
    private ?string $region = null;

    #[ORM\Column(length: 255)]
    private ?string $credo = null;

    #[ORM\Column(length: 7, nullable: true)]
    private ?string $colorCode = null;

    // ➜ propriété renommée en minuscule pour correspondre à mappedBy="teamVilain"
    #[ORM\OneToMany(targetEntity: Sbire::class, mappedBy: 'teamVilain')]
    private Collection $sbires;

    public function __construct()
    {
        $this->sbires = new ArrayCollection();
    }

    /* -------------------- getters / setters -------------------- */

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): static
    {
        $this->name = $name;
        return $this;
    }

    public function getRegion(): ?string
    {
        return $this->region;
    }

    public function setRegion(?string $region): static
    {
        $this->region = $region;
        return $this;
    }

    public function getCredo(): ?string
    {
        return $this->credo;
    }

    public function setCredo(string $credo): static
    {
        $this->credo = $credo;
        return $this;
    }

    public function getColorCode(): ?string
    {
        return $this->colorCode;
    }

    public function setColorCode(?string $colorCode): static
    {
        $this->colorCode = $colorCode;
        return $this;
    }

    /**
     * @return Collection<int, Sbire>
     */
    public function getSbires(): Collection
    {
        return $this->sbires;
    }

    public function addSbire(Sbire $sbire): static
    {
        if (!$this->sbires->contains($sbire)) {
            $this->sbires->add($sbire);
            $sbire->setTeamVilain($this);
        }
        return $this;
    }

    public function removeSbire(Sbire $sbire): static
    {
        if ($this->sbires->removeElement($sbire)) {
            if ($sbire->getTeamVilain() === $this) {
                $sbire->setTeamVilain(null);
            }
        }
        return $this;
    }
}