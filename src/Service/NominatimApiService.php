<?php
namespace App\Service;


use Symfony\Contracts\HttpClient\HttpClientInterface;


class NominatimApiService
{

    private $client;


    public function __construct(HttpClientInterface $client)
    {
        $this->client = $client;

    }

    /**
     * Return coordinates from nominatim Api
     */
    public function getCoordinates($city)
    {

        // déclenche une requête asynchrone
        $response = $this->client->request(
            // méthode htttp
            'GET',
            // url de l'api
            'https://nominatim.openstreetmap.org/search',
            [
                // le paramètres ici est la recherche par ville
                "query" => [
                    "q" => $city
                ]
            ]
        );

        // je recupere toutes les donnees coordinates 
        $cityAllCoordinates = $response->toArray();
        // dd($cityAllCoordinates);
        // je cree un tableau cityCoordinates pour y recuperer uniquement la latitude et longitude
        $cityCoordinates = [];
        $cityCoordinates[ "lat" ] = $cityAllCoordinates[ 0 ][ "lat" ];
        $cityCoordinates[ "lon" ] = $cityAllCoordinates[ 0 ][ "lon" ];
        // je retourne ce tableau 

        return $cityCoordinates;

    }

}