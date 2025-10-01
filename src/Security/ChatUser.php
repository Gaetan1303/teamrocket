<?php

namespace App\Security;

use Symfony\Component\Security\Core\User\UserInterface;

final class ChatUser implements UserInterface
{
    public function __construct(private string $codename)
    {
    }

    public function getUsername(): string
    {
        return $this->codename;
    }

    public function getUserIdentifier(): string
    {
        return $this->codename;
    }

    public function getRoles(): array
    {
        return ['ROLE_USER'];
    }

    public function getPassword(): ?string
    {
        return null;
    }

    public function getSalt(): ?string
    {
        return null;
    }

    public function eraseCredentials(): void
    {
        // nothing to do
    }

    public function __toString(): string
    {
        return $this->codename;
    }
}
