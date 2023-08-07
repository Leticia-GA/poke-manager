<?php

namespace App\Entity;

use App\Repository\PokemonRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: PokemonRepository::class)]
class Pokemon
{
    #[ORM\Id]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 100)]
    private ?string $name = null;

    #[ORM\Column(length: 50)]
    private ?string $color = null;

    #[ORM\ManyToOne(targetEntity: Pokemon::class)]
    private ?Pokemon $evolvesTo = null;

    public function __construct(?int $id, ?string $name, ?string $color)
    {
        $this->id = $id;
        $this->name = $name;
        $this->color = $color;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function getColor(): ?string
    {
        return $this->color;
    }

    public function setEvolvesTo(?Pokemon $evolvesTo): void
    {
        $this->evolvesTo = $evolvesTo;
    }
}