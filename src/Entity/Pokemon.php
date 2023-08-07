<?php

namespace App\Entity;

use App\Repository\PokemonRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: PokemonRepository::class)]
class Pokemon
{
    #[ORM\Id]
    #[ORM\Column]
    private ?int $id;

    #[ORM\Column(length: 100)]
    private ?string $name;

    #[ORM\Column(length: 50)]
    private ?string $color;

    #[ORM\ManyToMany(targetEntity: PokemonType::class, mappedBy: 'pokemons')]
    private Collection $types;

    #[ORM\ManyToOne(targetEntity: Pokemon::class)]
    private ?Pokemon $evolvesTo = null;

    #[ORM\Column(type: 'boolean', options: ['default' => true])]
    private bool $hasEvolution = true;

    #[ORM\Column(length: 255)]
    private string $evolutionChainUrl;

    public function __construct(int $id, string $name, string $color, string $evolutionChainUrl)
    {
        $this->id = $id;
        $this->name = $name;
        $this->color = $color;
        $this->types = new ArrayCollection();
        $this->evolutionChainUrl = $evolutionChainUrl;
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

    public function getTypes(): Collection
    {
        return $this->types;
    }

    public function addType(PokemonType $type): void
    {
        $this->types->add($type);
        $type->addPokemon($this);
    }

    public function getEvolvesTo(): ?Pokemon
    {
        return $this->evolvesTo;
    }

    public function setEvolvesTo(?Pokemon $evolvesTo): void
    {
        $this->evolvesTo = $evolvesTo;
    }

    public function hasEvolution(): bool
    {
        return $this->hasEvolution;
    }

    public function setHasEvolution(bool $hasEvolution): void
    {
        $this->hasEvolution = $hasEvolution;
    }

    public function getEvolutionChainUrl(): string
    {
        return $this->evolutionChainUrl;
    }

    public function getSerializedTypes(): string
    {
        $serialization = '';
        $types = $this->getTypes();
        $firstType = true;

        foreach ($types as $type) {
            if (!$firstType) {
                $serialization .= ', ';
            }

            $serialization .= $type->getName();
            $firstType = false;
        }

        return $serialization;
    }
}