<?php
namespace App\Service;


use Symfony\Contracts\HttpClient\HttpClientInterface;


class UnsplashApiService
{

    private $client;
    private $apiKey;

    public function __construct(HttpClientInterface $client, string $apiKey)
    {
        $this->client = $client;
        $this->apiKey = $apiKey;
    }

    /**
     * Return photos from unsplash
     */
    public function fetchPhotos($search)
    {

        // déclenche une requête asynchrone
        $response = $this->client->request(
            // méthode htttp
            'GET',
            // url de l'api
            'https://api.unsplash.com/search/photos',
            [
                // les paramètres ici la clé api et la recherche par mot-clé
                "query" => [
                    "client_id" => $this->apiKey,
                    "query"     => $search

                ]
            ]
        );

        return $response->toArray();

    }
    public function fetchPhotosRandom()
    {

        // déclenche une requête asynchrone
        $response = $this->client->request(
            // méthode htttp
            'GET',
            // url de l'api
            'https://api.unsplash.com/photos/random',
            [
                // les paramètres ici la clé api et la recherche par mot-clé
                "query" => [
                    "client_id" => $this->apiKey,
                  

                ]
            ]
        );

        return $response;

    }
  
}