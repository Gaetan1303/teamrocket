<?php

namespace App\Entity;

use App\Repository\PokemonRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: PokemonRepository::class)]
#[ORM\Table(name: 'pokemon')]
class Pokemon
{
    /* ---------- Constantes pour le statut ---------- */
    public const STATUS_WILD = 'Sauvage';
    public const STATUS_CAUGHT = 'Capturé';
    public const STATUS_STOLEN = 'Volé';

    /* ---------- Identifiant ---------- */
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    /* ---------- Données de base ---------- */
    #[ORM\Column(length: 50)]
    private ?string $name = null;

    #[ORM\Column(length: 50, unique: true)]
    private ?string $apiId = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $spriteFront = null;

    #[ORM\Column(type: 'json', nullable: true)]
    private array $types = [];

    #[ORM\Column(type: 'integer', nullable: true)]
    private ?int $baseExperience = null;

    #[ORM\Column(type: 'integer', nullable: true)]
    private ?int $height = null;

    #[ORM\Column(type: 'integer', nullable: true)]
    private ?int $weight = null;

    /* ---------- Statistiques ---------- */
    #[ORM\Column(type: 'integer', nullable: true)]
    private ?int $hp = null;

    #[ORM\Column(type: 'integer', nullable: true)]
    private ?int $attack = null;

    #[ORM\Column(type: 'integer', nullable: true)]
    private ?int $defense = null;

    #[ORM\Column(type: 'integer', nullable: true)]
    private ?int $specialAttack = null;

    #[ORM\Column(type: 'integer', nullable: true)]
    private ?int $specialDefense = null;

    #[ORM\Column(type: 'integer', nullable: true)]
    private ?int $speed = null;

    /* ---------- Statut d’obtention ---------- */
    #[ORM\Column(length: 20)]
    private string $status = self::STATUS_WILD;

    /* ---------- Getters & Setters ---------- */

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

    public function getApiId(): ?string
    {
        return $this->apiId;
    }

    public function setApiId(string $apiId): self
    {
        $this->apiId = $apiId;

        return $this;
    }

    public function getSpriteFront(): ?string
    {
        return $this->spriteFront;
    }

    public function setSpriteFront(?string $spriteFront): self
    {
        $this->spriteFront = $spriteFront;

        return $this;
    }

    public function getTypes(): array
    {
        return $this->types;
    }

    public function setTypes(?array $types): self
    {
        $this->types = $types ?? [];

        return $this;
    }

    public function getBaseExperience(): ?int
    {
        return $this->baseExperience;
    }

    public function setBaseExperience(?int $baseExperience): self
    {
        $this->baseExperience = $baseExperience;

        return $this;
    }

    public function getHeight(): ?int
    {
        return $this->height;
    }

    public function setHeight(?int $height): self
    {
        $this->height = $height;

        return $this;
    }

    public function getWeight(): ?int
    {
        return $this->weight;
    }

    public function setWeight(?int $weight): self
    {
        $this->weight = $weight;

        return $this;
    }

    public function getHp(): ?int
    {
        return $this->hp;
    }

    public function setHp(?int $hp): self
    {
        $this->hp = $hp;

        return $this;
    }

    public function getAttack(): ?int
    {
        return $this->attack;
    }

    public function setAttack(?int $attack): self
    {
        $this->attack = $attack;

        return $this;
    }

    public function getDefense(): ?int
    {
        return $this->defense;
    }

    public function setDefense(?int $defense): self
    {
        $this->defense = $defense;

        return $this;
    }

    public function getSpecialAttack(): ?int
    {
        return $this->specialAttack;
    }

    public function setSpecialAttack(?int $specialAttack): self
    {
        $this->specialAttack = $specialAttack;

        return $this;
    }

    public function getSpecialDefense(): ?int
    {
        return $this->specialDefense;
    }

    public function setSpecialDefense(?int $specialDefense): self
    {
        $this->specialDefense = $specialDefense;

        return $this;
    }

    public function getSpeed(): ?int
    {
        return $this->speed;
    }

    public function setSpeed(?int $speed): self
    {
        $this->speed = $speed;

        return $this;
    }

    public function getStatus(): string
    {
        return $this->status;
    }

    public function setStatus(string $status): self
    {
        if (!in_array($status, [self::STATUS_WILD, self::STATUS_CAUGHT, self::STATUS_STOLEN], true)) {
            throw new \InvalidArgumentException("Statut invalide : $status");
        }
        $this->status = $status;

        return $this;
    }
}
