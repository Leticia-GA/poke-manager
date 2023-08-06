<?php

namespace App\Controller;

use App\Entity\Pokemon;
use App\PokeApi\PokeApiClient;
use App\Repository\PokemonRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

#[Route(path: '/api')]
class PokemonController extends AbstractController
{
    private PokemonRepository $repository;
    private PokeApiClient $client;

    public function __construct(PokemonRepository $repository, PokeApiClient $client)
    {
        $this->repository = $repository;
        $this->client = $client;
    }

    #[Route('/load-all', name: 'load_all_first_generation', methods:['GET'])]
    public function loadAllFirstGeneration(EntityManagerInterface $entityManager): JsonResponse
    {
        $species = $this->client->getPokemonSpecies();

        foreach ($species as $specie) {
            $pokemonData = $this->client->getPokemonData($specie['url']);

            $pokemon = new Pokemon(
                $pokemonData['id'],
                $pokemonData['name'],
                $pokemonData['color']['name']
            );

            $entityManager->persist($pokemon);
        }

        $entityManager->flush();

        return new JsonResponse();
    }
}