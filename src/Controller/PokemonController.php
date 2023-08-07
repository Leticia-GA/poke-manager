<?php

namespace App\Controller;

use App\Entity\Pokemon;
use App\Entity\PokemonType;
use App\PokeApi\PokeApiClient;
use App\Repository\PokemonRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

#[Route(path: '/api')]
class PokemonController extends AbstractController
{
    private const NUM_ITEMS_PER_PAGE = 20;
    private const NUM_MAX_EVOLUTION_POKEMONS = 3;

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
                $pokemonData['color']['name'],
                $pokemonData['evolution_chain']['url']
            );

            $entityManager->persist($pokemon);
        }

        $entityManager->flush();

        return new JsonResponse();
    }

    #[Route('/load-types', name: 'load_types', methods:['GET'])]
    public function loadTypes(EntityManagerInterface $entityManager): JsonResponse
    {
        $types = $this->client->getPokemonTypes();

        foreach ($types as $type) {
            // Se obtienen los datos más detallados de cada tipo de pokemon
            $typeData = $this->client->getTypeData($type['url']);

            $pokemonType = new PokemonType(
                $typeData['id'],
                $typeData['name']
            );

            $pokemons = $typeData['pokemon'];

            // Se asigna el tipo a sus respectivos pokemons
            foreach ($pokemons as $pokemon) {
                $pokemonName = $pokemon['pokemon']['name'];

                $pokemonRepository = $entityManager->getRepository(Pokemon::class);
                $pokemon = $pokemonRepository->findOneBy(['name' => $pokemonName]);

                if ($pokemon) {
                    $pokemon->addType($pokemonType);
                }
            }

            $entityManager->persist($pokemonType);
        }

        $entityManager->flush();

        return new JsonResponse();
    }

    #[Route('/load-evolutions', name: 'load_evolutions', methods:['GET'])]
    public function loadEvolutions(EntityManagerInterface $entityManager): JsonResponse
    {
        $repository = $entityManager->getRepository(Pokemon::class);
        $pokemons = $repository->findAll();

        foreach ($pokemons as $pokemon) {
            if ($pokemon->getEvolvesTo() || !$pokemon->hasEvolution()) {
                continue;
            }

            $evolutionChain = $this->client->getEvolutionChain($pokemon->getEvolutionChainUrl());

            if (!isset($evolutionChain['evolves_to'][0])) {
                $pokemon->setHasEvolution(false);

                continue;
            }

            // Se guarda la referencia al pokemon original para poder
            // incrementar su número de evoluciones en base de datos
            $currentPokemon = $pokemon;

            $evolution = $evolutionChain['evolves_to'][0];

            while ($evolution) {
                $evolutionName = $evolution['species']['name'];
                $pokemonEvolution = $repository->findOneBy(['name' => $evolutionName]);

                if ($pokemonEvolution) {
                    $pokemon->setEvolvesTo($pokemonEvolution);
                    $currentPokemon->incrementNumEvolutions();
                } else {
                    // Si la evolución no pertenece a la primera
                    // generación de pokemon, se declara sin evolución
                    $pokemon->setHasEvolution(false);

                    break;
                }

                if (isset($evolution['evolves_to'][0])) {
                    // Se obtiene la siguiente evolución del pokemon para ser tratada dentro del while
                    $evolution = $evolution['evolves_to'][0];
                    $pokemon = $pokemonEvolution;
                } else {
                    $evolution = null;
                    $pokemonEvolution->setHasEvolution(false);
                }
            }
        }

        $entityManager->flush();

        return new JsonResponse();
    }

    #[Route('/pokemons', name:'get_all_pokemons', methods: ['GET'])]
    public function getAll(EntityManagerInterface $entityManager, Request $request): JsonResponse
    {
        $data = [];
        $page = $request->query->get('page', 1);

        if ($page < 1) {
            $page = 1;
        }

        $offset = ($page - 1) * self::NUM_ITEMS_PER_PAGE;

        $repository = $entityManager->getRepository(Pokemon::class);
        $pokemons = $repository->findBy([], ['id' => 'ASC'], self::NUM_ITEMS_PER_PAGE, $offset);

        foreach ($pokemons as $pokemon) {
            $data[] = [
                'id' => $pokemon->getId(),
                'name' => $pokemon->getName(),
                'color' => $pokemon->getColor(),
                'type' => $pokemon->getSerializedTypes(),
                'evolves_to' => $pokemon->getEvolvesTo() ? $pokemon->getEvolvesTo()->getName() : '-'
            ];
        }

        return new JsonResponse($data);
    }

    #[Route('/pokemon/{name}/evolutions', name:'get_pokemon_evolutions_by_name', methods: ['GET'])]
    public function getEvolutionsByName(string $name, EntityManagerInterface $entityManager): JsonResponse
    {
        $repository = $entityManager->getRepository(Pokemon::class);
        $pokemon = $repository->findOneBy(['name' => $name]);

        return new JsonResponse($pokemon->getEvolutions());
    }

    #[Route('/pokemon/max-evolutions', name:'get_pokemon_max_num_evolutions', methods: ['GET'])]
    public function getMaxNumEvolutions(EntityManagerInterface $entityManager): JsonResponse
    {
        $repository = $entityManager->getRepository(Pokemon::class);

        // Se ordena en primer lugar por el número de evoluciones que tiene el
        // pokemon y a continuación por el índice, tal y como pide el enunciado
        $pokemons = $repository->findBy([], ['numEvolutions' => 'DESC', 'id' => 'ASC'], self::NUM_MAX_EVOLUTION_POKEMONS);

        $evolutions = [];

        foreach ($pokemons as $pokemon) {
            $evolutions[] = [
                'id' => $pokemon->getId(),
                'name' => $pokemon->getName(),
                'number_evolutions' => $pokemon->getNumEvolutions(),
                'evolutions' => $pokemon->getEvolutions()
            ];
        }

        return new JsonResponse($evolutions);
    }
}