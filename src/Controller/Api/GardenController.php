<?php

namespace App\Controller\Api;

use App\Entity\Garden;
use App\Repository\GardenRepository;
use App\Service\NominatimApiService;
use DateTimeImmutable;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\ORMInvalidArgumentException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\Exception\NotEncodableValueException;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class GardenController extends AbstractController
{
    private $nominatimApi;

    public function __construct(NominatimApiService $nominatimApi)
    {
        $this->nominatimApi = $nominatimApi;
    }

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

        $cityApi = $this->nominatimApi->getCoordinates($garden->getAddress() . " " . $garden->getPostalCode() . " " .$garden->getCity());

        $garden->setLat($cityApi['lat']);
        $garden->setLon($cityApi['lon']);
        
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

    /**
     * @Route("/api/gardens/{id}", name="app_api_garden_putGardenById", methods={"PUT"})
     */
    public function putGardenById(Garden $garden, GardenRepository $gardenRepository, Request $request, SerializerInterface $serializer, ValidatorInterface $validator, EntityManagerInterface $em): JsonResponse
    {
        $garden = $gardenRepository->find($garden);

        if (!$garden) {
            return $this->json(["error" => "le jardin n'existe pas"], Response::HTTP_BAD_REQUEST);
        }

        $jsonContent = $request->getContent();

        try {
            $updatedGarden = $serializer->deserialize($jsonContent, Garden::class, 'json', [AbstractNormalizer::OBJECT_TO_POPULATE => $garden]);


        } catch (NotEncodableValueException $e) {

            return $this->json(["error" => "JSON INVALID"], Response::HTTP_BAD_REQUEST);
        }

        $cityApi = $this->nominatimApi->getCoordinates($garden->getAddress() . " " .$garden->getCity());

        $garden->setLat($cityApi['lat']);
        $garden->setLon($cityApi['lon']);

        $garden->setUpdatedAt(new DateTimeImmutable());

        $errors = $validator->validate($updatedGarden);
        
        if (count($errors) > 0) {
            return $this->json($errors, 400);
        }

        $em->persist($updatedGarden);
        $em->flush();

        return $this->json($updatedGarden, Response::HTTP_OK, [], ["groups" => "gardensWithRelations"]);
    }
    

    /**
     * @Route("/api/gardens/{id}", name="app_api_garden_deleteGardenById", methods={"DELETE"})
     */
    public function deleteGardenById(Garden $garden, GardenRepository $gardenRepository, EntityManagerInterface $em): JsonResponse
    {
        try {
            $gardenRepository->remove($garden, true);
            
        } catch (ORMInvalidArgumentException $e) {

            return $this->json(["error" => "le jardin n'existe pas"], Response::HTTP_BAD_REQUEST);
        }
        
        return $this->json("Le jardin a bien été supprimé", Response::HTTP_NO_CONTENT);
    }

    /**
     * @Route("/api/search/gardens", name="app_api_garden_getGardensBySearch", methods={"GET"})
     */
    public function getGardensBySearch(Request $request, GardenRepository $gardenRepository): JsonResponse
    {
        $dataApi = $this->nominatimApi->getCoordinates($request->query->get('city'));

        $cityLat = $dataApi['lat'];
        $cityLon = $dataApi['lon'];

        $dataDist = $request->query->get('dist');

        $gardens = $gardenRepository->findGardenByCoordonates($cityLat, $cityLon, $dataDist);

        return $this->json($gardens, Response::HTTP_OK);
    }
}
