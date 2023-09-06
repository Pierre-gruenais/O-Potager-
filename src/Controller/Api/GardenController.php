<?php

namespace App\Controller\Api;

use App\Entity\Garden;
use App\Entity\Picture;
use App\Repository\GardenRepository;
use App\Repository\PictureRepository;
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
 * @Route("/api/gardens")
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

        $coordinatesCityApi = $this->nominatimApi->getCoordinates($request->query->get('city'));
        
        if ($coordinatesCityApi == false){
            return $this->json(['error' => "La ville que vous recherchez est introuvable."], Response::HTTP_BAD_REQUEST);
        };
        
        $cityLat = $coordinatesCityApi[ 'lat' ];
        $cityLon = $coordinatesCityApi[ 'lon' ];

        $distance = $request->query->get('dist');

        if ($distance === null){
            $distance = 10;
        }

        $gardens = $gardenRepository->findGardensByCoordonates($cityLat, $cityLon, $distance);

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

        $coordinatesCityApi = $this->nominatimApi->getCoordinates($garden->getCity(), $garden->getAddress());
        
        if ($coordinatesCityApi == false){
            return $this->json(['error' => "L'adresse est introuvable"], Response::HTTP_BAD_REQUEST);
        };

        $garden->setLat($coordinatesCityApi['lat']);
        $garden->setLon($coordinatesCityApi['lon']);
        
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

        $coordinatesCityApi = $this->nominatimApi->getCoordinates($garden->getCity(), $garden->getAddress());
        
        if ($coordinatesCityApi == false){
            return $this->json(['error' => "L'adresse est introuvable"], Response::HTTP_BAD_REQUEST);
        };

        $garden->setLat($coordinatesCityApi['lat']);
        $garden->setLon($coordinatesCityApi['lon']);

        
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
     * Delete a garden by id
     * 
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


    /**
     * add new picture to garden
     * 
     * @Route("/{id}/pictures", name="app_api_garden_addPictureToRegisteredGarden", methods={"POST"}) 
     * 
     * @param Garden $garden id of the garden
     * @param GardenRepository $gardenRepository
     * @param EntityManagerInterface $em
     * @return JsonResponse
     */
    public function addPictureToRegisteredGarden(Garden $garden, Request $request, SerializerInterface $serializer, EntityManagerInterface $em): JsonResponse
    {

        $jsonContent = $request->getContent();

        $picture = $serializer->deserialize($jsonContent, Picture::class, 'json');

        $dataErrors = $this->validatorError->returnErrors($garden);

        if ($dataErrors) {
            return $this->json($dataErrors, Response::HTTP_UNPROCESSABLE_ENTITY);
        }
        $picture->setGarden($garden);
        $em->persist($picture);
        $em->flush();

        return $this->json(
            "L'image a bien été ajoutée au jardin ",
            Response::HTTP_CREATED,
            ["Location" => $this->generateUrl("app_api_garden_getGardenById", ["id" => $garden->getId()])],
            ["groups" => "gardensWithRelations"]
        );

    }

    /**
     * delete picture by id
     * 
     * @Route("/pictures/{id}", name="app_api_garden_deletePictureFromRegisteredGarden", methods={"DELETE"})
     *
     * @param Picture $picture id of the picture
     * @param PictureRepository $pictureRepository
     * @return JsonResponse
     */
    public function deletePictureFromRegisteredGarden(Picture $picture, PictureRepository $pictureRepository): JsonResponse
    {
        try {

            $pictureRepository->remove($picture, true);

        } catch (ORMInvalidArgumentException $e) {

            return $this->json(["error" => "L'image n'existe pas"], Response::HTTP_BAD_REQUEST);

        }

        return $this->json("L'image a bien été supprimée", Response::HTTP_OK);
    }

    /**
     * get pictures by garden Id
     * 
     * @Route("/{id}/pictures", name="app_api_garden_getPictureByGarden", methods={"GET"})
     *
     * @param integer $id 
     * @param PictureRepository $pictureRepository
     * @return JsonResponse
     */
    public function getPictureByGarden(int $id, PictureRepository $pictureRepository): JsonResponse
    {
        $pictures = $pictureRepository->findBy(['garden' => $id]);

        return $this->json($pictures, Response::HTTP_OK);
    }
}