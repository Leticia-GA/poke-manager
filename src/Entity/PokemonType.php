<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
class PokemonType
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id;

    #[ORM\Column(length: 100)]
    private ?string $name;

    #[ORM\ManyToMany(targetEntity: Pokemon::class, inversedBy: 'types')]
    private Collection $pokemons;

    public function __construct(int $id, string $name)
    {
        $this->id = $id;
        $this->name = $name;
        $this->pokemons = new ArrayCollection();
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function addPokemon(Pokemon $pokemon): bool
    {
        return $this->pokemons->add($pokemon);
    }
}