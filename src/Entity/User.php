<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Doctrine\ORM\Mapping\Table;

 #[ORM\Entity(repositoryClass: UserRepository::class)]
#[Table(name: 'app_user')]
#[UniqueEntity(fields: ['email'], message: 'There is already an account with this email')]
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 180, unique: true)]
    private ?string $email = null;

    #[ORM\Column(length: 20, unique: true)]
    private ?string $codename = null;

    #[ORM\Column]
    private array $roles = [];

    #[ORM\Column]
    private ?string $password = null;

    #[ORM\Column(length: 36, unique: true)]
    private ?string $uuid = null;

    #[ORM\ManyToOne(targetEntity: TeamVilain::class, inversedBy: 'sbires')]
    private ?TeamVilain $teamVilain = null;

    #[ORM\Column]
    private bool $isVerified = false;

    #[ORM\Column(length: 50, nullable: true)]
    private ?string $starterPokemon = null;

    #[ORM\Column(nullable: true)]
    private ?int $starterPokemonId = null;

    #[ORM\Column(options: ["default" => false])]
    private bool $hasDoneFirstTheft = false;

    public function __construct()
    {
        $this->uuid = \Symfony\Component\Uid\Uuid::v4()->toRfc4122();
    }

    // -------- getters & setters --------

    public function getId(): ?int { return $this->id; }

    public function getEmail(): ?string { return $this->email; }
    public function setEmail(string $email): static { $this->email = $email; return $this; }

    public function getUserIdentifier(): string { return (string) $this->email; }

    public function getCodename(): ?string { return $this->codename; }
    public function setCodename(string $codename): static { $this->codename = $codename; return $this; }

    public function getRoles(): array {
        $roles = $this->roles;
        $roles[] = 'ROLE_USER';
        return array_unique($roles);
    }
    public function setRoles(array $roles): static { $this->roles = $roles; return $this; }

    public function getPassword(): ?string { return $this->password; }
    public function setPassword(string $password): static { $this->password = $password; return $this; }

    public function getSalt(): ?string { return null; }
    public function eraseCredentials(): void {}

    public function getUuid(): ?string { return $this->uuid; }
    public function setUuid(string $uuid): static { $this->uuid = $uuid; return $this; }

    public function getTeamVilain(): ?TeamVilain { return $this->teamVilain; }
    public function setTeamVilain(?TeamVilain $teamVilain): static { $this->teamVilain = $teamVilain; return $this; }

    public function isVerified(): bool { return $this->isVerified; }
    public function setIsVerified(bool $isVerified): static { $this->isVerified = $isVerified; return $this; }

    public function getStarterPokemon(): ?string { return $this->starterPokemon; }
    public function setStarterPokemon(?string $starter): static { $this->starterPokemon = $starter; return $this; }

    public function getStarterPokemonId(): ?int { return $this->starterPokemonId; }
    public function setStarterPokemonId(?int $id): static { $this->starterPokemonId = $id; return $this; }

    public function hasDoneFirstTheft(): bool { return $this->hasDoneFirstTheft; }
    public function setHasDoneFirstTheft(bool $done): static { $this->hasDoneFirstTheft = $done; return $this; }
}