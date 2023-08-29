<?php

namespace App\Controller\Api;

use App\Entity\Garden;
use App\Repository\GardenRepository;
use App\Service\NominatimApiService;
use App\Service\ValidatorErrorService;
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

/**
 * @Route("/gardens")
 */
class GardenController extends AbstractController
{
    private $nominatimApi;
    private $validatorError;

    /**
     * Construct of the class
     *
     * @param NominatimApiService $nominatimApi NominatimAPI call service
     * @param ValidatorErrorService $validatorError ValidatorError call service
     */
    public function __construct(NominatimApiService $nominatimApi, ValidatorErrorService $validatorError)
    {
        $this->nominatimApi = $nominatimApi;
        $this->validatorError = $validatorError;
    }

    /**
     * Route for retrieving all garden data
     * 
     * @Route("/", name="app_api_garden_getGardens", methods={"GET"})
     * 
     * @param GardenRepository $gardenRepository
     * @return JsonResponse
     */
    public function getGardens(GardenRepository $gardenRepository): JsonResponse
    {
        $gardens = $gardenRepository->findAll();

        return $this->json($gardens, Response::HTTP_OK, [], ["groups" => "gardensWithRelations"]);
    }

    /**
     * Route to retrieve all garden data relative to a distance
     * 
     * @Route("/search", name="app_api_garden_getGardensBySearch", methods={"GET"})
     * 
     * @param Request $request
     * @param GardenRepository $gardenRepository
     * @return JsonResponse
     */
    public function getGardensBySearch(Request $request, GardenRepository $gardenRepository): JsonResponse
    {
        $coordonatesCityApi = $this->nominatimApi->getCoordinates($request->query->get('city'));
        $cityLat = $coordonatesCityApi[ 'lat' ];
        $cityLon = $coordonatesCityApi[ 'lon' ];

        $distance = $request->query->get('dist');

        $gardens = $gardenRepository->findGardenByCoordonates($cityLat, $cityLon, $distance);

        return $this->json($gardens, Response::HTTP_OK);
    }


    /**
     * Route used to retrieve all the data for a garden by id
     * 
     * @Route("/{id}", name="app_api_garden_getGardenById", methods={"GET"})
     * 
     * @param Garden $garden id of the garden
     * @return JsonResponse
     */
    public function getGardenById(Garden $garden): JsonResponse
    {
        return $this->json($garden, Response::HTTP_OK, [], ["groups" => "gardensWithRelations"]);
    }

    /**
     * Path add a garden 
     *
     * @Route("/", name="app_api_garden_postGarden", methods={"POST"})
     * 
     * @param Request $request
     * @param SerializerInterface $serializer
     * @param EntityManagerInterface $entityManager
     * @return JsonResponse
     */
    public function postGarden(Request $request, SerializerInterface $serializer, EntityManagerInterface $em): JsonResponse
    {
        $jsonContent = $request->getContent();

        $garden = $serializer->deserialize($jsonContent, Garden::class, 'json');

        $coordonnatesCityApi = $this->nominatimApi->getCoordinates($garden->getAddress() . " " .$garden->getCity());
        $garden->setLat($coordonnatesCityApi['lat']);
        $garden->setLon($coordonnatesCityApi['lon']);
        
        $dataErrors = $this->validatorError->returnErrors($garden);

        if ($dataErrors) {
            return $this->json($dataErrors, Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        $em->persist($garden);
        $em->flush();

        return $this->json([$garden], Response::HTTP_CREATED, [
            "Location" => $this->generateUrl("app_api_garden_getGardenById", ["id" => $garden->getId()])
        ], [
                "groups" => "gardensWithRelations"
            ]);
    }

    /**
     * path to update a garden
     * 
     * @Route("/{id}", name="app_api_garden_putGardenById", methods={"PUT"})
     *
     * @param Garden $garden id of the garden
     * @param GardenRepository $gardenRepository
     * @param Request $request
     * @param SerializerInterface $serializer
     * @param EntityManagerInterface $em
     * @return JsonResponse
     */
    public function putGardenById(Garden $garden, GardenRepository $gardenRepository, Request $request, SerializerInterface $serializer, EntityManagerInterface $em): JsonResponse
    {
        $garden = $gardenRepository->find($garden);

        if (!$garden) {
            return $this->json(["error" => "Le jardin n'existe pas"], Response::HTTP_BAD_REQUEST);
        }

        $jsonContent = $request->getContent();

        $updatedGarden = $serializer->deserialize($jsonContent, Garden::class, 'json', [AbstractNormalizer::OBJECT_TO_POPULATE => $garden]);

        $coordonatesCityApi = $this->nominatimApi->getCoordinates($garden->getAddress() . " " .$garden->getCity());
        $garden->setLat($coordonatesCityApi['lat']);
        $garden->setLon($coordonatesCityApi['lon']);
        $garden->setUpdatedAt(new DateTimeImmutable());

        $dataErrors = $this->validatorError->returnErrors($garden);

        if ($dataErrors) {
            return $this->json($dataErrors, Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        $em->persist($updatedGarden);
        $em->flush();

        return $this->json($updatedGarden, Response::HTTP_OK, [], ["groups" => "gardensWithRelations"]);
    }


    /**
     * @Route("/{id}", name="app_api_garden_deleteGardenById", methods={"DELETE"})
     *
     * @param Garden $garden id of the garden
     * @param GardenRepository $gardenRepository
     * @param EntityManagerInterface $em
     * @return JsonResponse
     */
    public function deleteGardenById(Garden $garden, GardenRepository $gardenRepository, EntityManagerInterface $em): JsonResponse
    {
        try {

            $gardenRepository->remove($garden, true);

        } catch (ORMInvalidArgumentException $e) {

            return $this->json(["error" => "Le jardin n'existe pas"], Response::HTTP_BAD_REQUEST);

        }
        
        return $this->json("Le jardin a bien été supprimé", Response::HTTP_OK);
    }
}