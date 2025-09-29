<?php

namespace App\Entity;

use App\Repository\TeamVilainRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * Entité représentant une « équipe de vilains » dans le système.
 * 
 * Une TeamVilain est identifiée de façon unique par son nom, possède une
 * région d’activité, un credo (sa « devise » ou philosophie), un code couleur
 * hexadécimal optionnel et peut contenir plusieurs membres.
 */
#[ORM\Entity(repositoryClass: TeamVilainRepository::class)]
class TeamVilain
{
    /**
     * Identifiant technique auto-généré par Doctrine.
     * @var int|null
     */
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    /**
     * Nom de l’équipe (unique dans la base).
     * @var string|null
     */
    #[ORM\Column(length: 50, unique: true)]
    private ?string $name = null;

    /**
     * Région géographique ou zone d’influence de l’équipe.
     * @var string|null
     */
    #[ORM\Column(length: 100, nullable: true)]
    private ?string $region = null;

    /**
     * Credo / devise de l’équipe.
     * @var string|null
     */
    #[ORM\Column(length: 255)]
    private ?string $credo = null;

    /**
     * Code couleur au format hexadécimal (ex: « #FF0000 »).
     * @var string|null
     */
    #[ORM\Column(length: 7, nullable: true)]
    private ?string $colorCode = null;

    /**
     * Collection des membres appartenant à cette équipe.
     * Relation OneToMany vers Sbire (l’inverse se trouve dans Sbire::$teamVilain).
     * @var Collection<int, Sbire>
     */
    #[ORM\OneToMany(targetEntity: Sbire::class, mappedBy: 'teamVilain')]
    private Collection $Sbires;

    /**
     * Constructeur : initialise la collection de membres.
     */
    public function __construct()
    {
        $this->Sbires = new ArrayCollection();
    }

    /* ──────────────── Getters & Setters ──────────────── */

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
     * Retourne la collection des sbires de l’équipe.
     * @return Collection<int, Sbire>
     */
    public function getSbires(): Collection
    {
        return $this->Sbires;
    }

    /**
     * Ajoute un sbire à l’équipe tout en maintenant la cohérence bidirectionnelle.
     */
    public function addSbire(Sbire $sbire): static
    {
        if (!$this->Sbires->contains($sbire)) {
            $this->Sbires->add($sbire);
            $sbire->setTeamVilain($this);
        }
        return $this;
    }

    /**
     * Retire un sbire de l’équipe et met à jour l’association inverse.
     */
    public function removeSbire(Sbire $sbire): static
    {
        if ($this->Sbires->removeElement($sbire)) {
            // On s’assure que le sbire ne pointe plus vers cette équipe
            if ($sbire->getTeamVilain() === $this) {
                $sbire->setTeamVilain(null);
            }
        }
        return $this;
    }
}