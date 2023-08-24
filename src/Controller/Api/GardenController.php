<?php

namespace App\Controller\Api;

use App\Entity\Garden;
use App\Repository\GardenRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\Exception\NotEncodableValueException;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class GardenController extends AbstractController
{
    /**
     * @Route("/api/gardens", name="app_api_garden_getGardens", methods={"GET"})
     */
    public function getGardens(GardenRepository $gardenRepository): JsonResponse
    {
        $gardens = $gardenRepository->findAll();

        return $this->json($gardens, Response::HTTP_OK, [], ["groups" => "gardensWithRelations"]);
    }

    /**
     * @Route("/api/gardens/{id}", name="app_api_garden_getGardenById", methods={"GET"})
     */
    public function getGardenById(Garden $garden): JsonResponse
    {
        return $this->json($garden, Response::HTTP_OK, [], ["groups" => "gardensWithRelations"]);
    }

    /**
     * @Route("/api/gardens", name="app_api_garden_postGarden", methods={"POST"})
     */
    public function postGarden(Request $request, SerializerInterface $serializer, ValidatorInterface $validator, EntityManagerInterface $entityManager): JsonResponse
    {
        $jsonContent = $request->getContent();

        try {

            $garden = $serializer->deserialize($jsonContent, Garden::class, 'json');
            //! array unique validation url (deux fois pas la meme !)
        } catch (NotEncodableValueException $e) {
        
            return $this->json(['error' => 'JSON INVALID'], Response::HTTP_BAD_REQUEST);
        
        }

        $errors = $validator->validate($garden);

        if (count($errors) > 0) {
            
            $dataErrors = [];

            foreach ($errors as $error) {
                $dataErrors[$error->getPropertyPath()][] = $error->getMessage();
            }

            return $this->json($dataErrors, Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        $entityManager ->persist($garden);
        $entityManager->flush();

        return $this->json([$garden], Response::HTTP_CREATED, [
            "Location" => $this->generateUrl("app_api_garden_getGardenById", ["id" => $garden->getId()])
        ], [
            "groups" => "gardensWithRelations"
        ]);
    }
}
