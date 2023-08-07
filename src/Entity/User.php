<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * @method string getUserIdentifier()
 */
#[ORM\Entity]
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    public const ROLE_USER = 'ROLE_USER';
    public const ROLE_ADMIN = 'ROLE_ADMIN';

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private int $id;

    #[ORM\Column(length: 100)]
    private string $name;

    #[ORM\Column(length: 150)]
    private string $email;

    #[ORM\Column(length: 100)]
    private string $password;

    #[ORM\Column(length: 50)]
    private string $rol;

    public function __construct(string $name, string $email, string $rol)
    {
        $this->name = $name;
        $this->email = $email;
        $this->rol = $rol;
    }

    public function getRoles(): array
    {
        return [$this->rol];
    }

    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function setPassword(string $password): void
    {
        $this->password = $password;
    }

    public function getSalt(): ?string
    {
        return null;
    }

    public function eraseCredentials() {}

    public function getUsername(): string
    {
        return $this->email;
    }

    public function __call(string $name, array $arguments) {}
}