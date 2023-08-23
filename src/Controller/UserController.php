<?php

namespace App\Controller;

use App\Service\NominatimApiService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Response;
class UserController extends AbstractController
{
    /**
     * @Route("/user", name="app_user")
     */
    public function index(): JsonResponse
    {
        return $this->json([
            'message' => 'Welcome to your new controller!',
            'path' => 'src/Controller/UserController.php',
        ]);
    }
     /**
     * @Route("/api/garden/{city}", name="app_user")
     */
    public function getCityCoordinates(string $city, NominatimApiService $nominatimService): JsonResponse
    {
        return $this->json($nominatimService->getCoordinates($city), Response::HTTP_OK, []);
        
    }
}
// job front :recupere tous les jardins puisque spa 

// job front : qd l utilisateur ecris une ville dans search on veut lui montrer 
//les jardins autours de cette coordonnee avec un rayon par deafult de 10km.

// le front peut t il recuperer les coordonnees de cette ville pour ensuite faire un tri a partir des coordonnes dans un cercle de 10km ?
// cad verifier dans le fetch de tous les jardins  si un jardin ou plusieurs a/ont des coordonnees qui rentre dans ce cercle.
//et fait apparaitre les jardins en asynchrone avec un filtre pour ne pas tous les afficher

// cote back lorsque l utilisateur soumet un jardin on doit recuperer le nom de la ville , 
//chercher ses coordonnes, pour ensuite rentrer ces donnees en bdd
