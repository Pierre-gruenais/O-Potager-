<?php

namespace App\Controller\Api;

use App\Repository\GardenRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class GardenController extends AbstractController
{
    /**
     * @Route("/api/gardens", name="app_api_garden_getGardens")
     */
    public function getGardens(GardenRepository $gardenRepository): JsonResponse
    {
        $gardens = $gardenRepository->findAll();

        return $this->json($gardens, Response::HTTP_OK, [], ["groups" => "gardens"]);
    }
}
