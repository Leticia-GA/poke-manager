<?php

namespace App\PokeApi;

use App\Entity\Pokemon;
use GuzzleHttp\Client;

class PokeApiClient
{
    private const LOAD_ALL_URL = 'https://pokeapi.co/api/v2/generation/1';
    private const LOAD_TYPES_URL = 'https://pokeapi.co/api/v2/type';
    private const LOAD_EVOLUTION_CHAIN_URL = 'https://pokeapi.co/api/v2/evolution-chain/{id}';

    public function getPokemonSpecies()
    {
        $client = new Client();
        $response = $client->request('GET', self::LOAD_ALL_URL);
        $body = json_decode($response->getBody(), true);

        return $body['pokemon_species'];
    }

    public function getPokemonData(string $url)
    {
        $client = new Client();
        $response = $client->request('GET', $url);

        return json_decode($response->getBody(), true);
    }

    public function getPokemonTypes()
    {
        $client = new Client();
        $response = $client->request('GET', self::LOAD_TYPES_URL);
        $body = json_decode($response->getBody(), true);

        return $body['results'];
    }

    public function getTypeData(string $url)
    {
        $client = new Client();
        $response = $client->request('GET', $url);

        return json_decode($response->getBody(), true);
    }

    public function getEvolutionChain(Pokemon $pokemon)
    {
        $client = new Client();
        $url = str_replace('{id}', $pokemon->getId(), self::LOAD_EVOLUTION_CHAIN_URL);
        $response = $client->request('GET', $url);
        $body = json_decode($response->getBody(), true);

        return $body['chain'];
    }
}