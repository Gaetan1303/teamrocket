<?php
// src/Entity/Sbire.php
namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'sbire')]
class Sbire
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\OneToOne(inversedBy: 'sbire', targetEntity: User::class)]
    #[ORM\JoinColumn(nullable: false, onDelete: 'CASCADE')]
    private User $user;

    #[ORM\Column(length: 100)]
    private string $picture = 'assets/images/characters/default-homme.png';

    #[ORM\Column(length: 7)]
    private string $color = '#000000';

    #[ORM\Column(length: 20)]
    private string $accessory = 'none';

    #[ORM\Column(type: 'smallint')]
    private int $power = 1;

    #[ORM\Column(type: 'smallint')]
    private int $defense = 1;

    #[ORM\Column(type: 'smallint')]
    private int $speed = 1;

    public function getId(): ?int { return $this->id; }
    public function getUser(): User { return $this->user; }
    public function setUser(User $user): self { $this->user = $user; return $this; }

    public function getPicture(): string { return $this->picture; }
    public function setPicture(string $picture): self { $this->picture = $picture; return $this; }

    public function getColor(): string { return $this->color; }
    public function setColor(string $color): self { $this->color = $color; return $this; }

    public function getAccessory(): string { return $this->accessory; }
    public function setAccessory(string $accessory): self { $this->accessory = $accessory; return $this; }

    public function getPower(): int { return $this->power; }
    public function setPower(int $power): self { $this->power = $power; return $this; }

    public function getDefense(): int { return $this->defense; }
    public function setDefense(int $defense): self { $this->defense = $defense; return $this; }

    public function getSpeed(): int { return $this->speed; }
    public function setSpeed(int $speed): self { $this->speed = $speed; return $this; }
}