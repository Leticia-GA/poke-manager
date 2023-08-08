<?php

namespace App\Controller;

use App\Entity\Pokemon;
use App\Entity\PokemonType;
use App\Entity\User;
use App\PokeApi\PokeApiClient;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Security;

#[Route(path: '/api')]
class PokemonController extends AbstractController
{
    private const NUM_ITEMS_PER_PAGE = 20;
    private const NUM_MAX_EVOLUTION_POKEMONS = 3;

    private EntityManagerInterface $entityManager;
    private PokeApiClient $client;

    public function __construct(EntityManagerInterface $entityManager, PokeApiClient $client)
    {
        $this->entityManager = $entityManager;
        $this->client = $client;
    }

    #[Route('/admin/load-pokemons', name: 'load_all_first_generation', methods:['GET'])]
    public function loadAll(): JsonResponse
    {
        $this->loadAllFirstGeneration();
        $this->loadTypes();
        $this->loadEvolutions();

        return new JsonResponse();
    }

    public function loadAllFirstGeneration(): void
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

            $this->entityManager->persist($pokemon);
        }

        $this->entityManager->flush();
    }

    public function loadTypes(): void
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

                $pokemonRepository = $this->entityManager->getRepository(Pokemon::class);
                $pokemon = $pokemonRepository->findOneBy(['name' => $pokemonName]);

                if ($pokemon) {
                    $pokemon->addType($pokemonType);
                }
            }

            $this->entityManager->persist($pokemonType);
        }

        $this->entityManager->flush();
    }

    public function loadEvolutions(): void
    {
        $repository = $this->entityManager->getRepository(Pokemon::class);
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

        $this->entityManager->flush();
    }

    #[Route('/pokemons', name:'get_all_pokemons', methods: ['GET'])]
    public function getAll(Request $request): JsonResponse
    {
        $data = [];
        $page = $request->query->get('page', 1);

        if ($page < 1) {
            $page = 1;
        }

        $offset = ($page - 1) * self::NUM_ITEMS_PER_PAGE;

        $repository = $this->entityManager->getRepository(Pokemon::class);
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
    public function getEvolutionsByName(string $name): JsonResponse
    {
        $repository = $this->entityManager->getRepository(Pokemon::class);
        $pokemon = $repository->findOneBy(['name' => $name]);

        if (!$pokemon) {
            return new JsonResponse(null, Response::HTTP_NOT_FOUND);
        }

        return new JsonResponse($pokemon->getEvolutions());
    }

    #[Route('/pokemon/max-evolutions', name:'get_pokemon_max_num_evolutions', methods: ['GET'])]
    public function getMaxNumEvolutions(): JsonResponse
    {
        $repository = $this->entityManager->getRepository(Pokemon::class);

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

    #[Route('/user/pokemons', name:'get_user_pokemons', methods: ['GET'])]
    public function getUserPokemons(Security $security): JsonResponse
    {
        $user = $security->getUser();
        $userRole = $user->getRoles();
        $userTypes = $user->getTypes();

        if (in_array('ROLE_ADMIN', $userRole)) {
            $pokemonTypeRepository = $this->entityManager->getRepository(PokemonType::class);
            $pokemonTypes = $pokemonTypeRepository->findAll();

            return new JsonResponse($this->getPokemonDataByTypes($pokemonTypes));
        }

        return new JsonResponse($this->getPokemonDataByTypes($userTypes));
    }

    private function getPokemonDataByTypes($types): array
    {
        $userPokemons = [];

        foreach ($types as $type) {
            $pokemonsByType = $type->getPokemons();

            foreach ($pokemonsByType as $pokemon) {
                $userPokemons[$type->getName()][] = [
                    'id' => $pokemon->getId(),
                    'name' => $pokemon->getName()
                ];
            }
        }

        return $userPokemons;
    }

    #[Route('/admin/user/{id}/update-types', name:'update_user_types', methods: ['POST'])]
    public function updateUserTypes(int $id, Request $request): JsonResponse
    {
        $userRepository = $this->entityManager->getRepository(User::class);
        $user = $userRepository->find($id);

        if (!$user) {
            return new JsonResponse(null, Response::HTTP_NOT_FOUND);
        }

        $pokemonTypeRepository = $this->entityManager->getRepository(PokemonType::class);
        $body = json_decode($request->getContent(), true);
        $typeIds = $body['types'];

        foreach ($typeIds as $typeId) {
            $type = $pokemonTypeRepository->find($typeId);

            if ($type) {
                $user->addType($type);
            }
        }

        $this->entityManager->flush();

        return new JsonResponse();
    }
}