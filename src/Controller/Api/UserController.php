<?php

namespace App\Controller\Api;

use App\Entity\Favorite;
use App\Entity\User;
use App\Repository\FavoriteRepository;
use App\Repository\GardenRepository;
use App\Repository\UserRepository;
use DateTimeImmutable;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\ORMInvalidArgumentException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Serializer\Exception\NotEncodableValueException;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;


class UserController extends AbstractController
{


    //! GET USERS
    /**
     * @Route("/api/users", name="app_api_user_getUsers", methods={"GET"})
     * Retrieve all datas of all users 
     */
    public function getUsers(UserRepository $userRepository): JsonResponse
    {
        // Find all users or return error
        $users = $userRepository->findAll();
        if (!$users) {
            return $this->json(["error" => "There are no users"], Response::HTTP_BAD_REQUEST);
        }
        return $this->json($users, Response::HTTP_OK, [], ["groups" => "users"]);
    }



    //! GET USER
    /**
     * @Route("/api/users/{id}", name="app_api_user_getUsersById", methods={"GET"})
     * Retrieve all datas of a user 
     */
    public function getUsersById(int $id, UserRepository $userRepository): JsonResponse
    {
        // Find user or return error
        $user = $userRepository->find($id);

        if (!$user) {
            return $this->json(["error" => "The user with ID " . $id . " does not exist"], Response::HTTP_BAD_REQUEST);
        }
        return $this->json($user, Response::HTTP_OK, [], ["groups" => "users"]);
    }



    //! POST USER
    /**
     * @Route("/api/users", name="app_api_user_postUsers", methods={"POST"})
     * Add new user in database
     */
    public function postUsers(Request $request, SerializerInterface $serializer, ValidatorInterface $validator, EntityManagerInterface $entityManager): JsonResponse
    {
        // Deserialize JSON content into User object 
        $jsonContent = $request->getContent();
        $user = $serializer->deserialize($jsonContent, User::class, 'json');

        // Validate User object  or return validation errors
        $errors = $validator->validate($user);
        if (count($errors) > 0) {
            $dataErrors = [];
            foreach ($errors as $error) {
                $dataErrors[$error->getPropertyPath()][] = $error->getMessage();
            }
            return $this->json($dataErrors, Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        // Post user and save changes into database
        $entityManager->persist($user);
        $entityManager->flush();

        // Return json with datas of new user 
        return $this->json([$user], Response::HTTP_CREATED, [
            "Location" => $this->generateUrl("app_api_user_getUsersById", ["id" => $user->getId()])
        ], [
                "groups" => "users"
            ]);
    }


    //! PUT USER
    /**
     * @Route("/api/users/{id}", name="app_api_user_putUser", methods={"PUT"})
     * Update one user
     */
    public function putUser(
        int $id,
        SerializerInterface $serializer,
        UserRepository $userRepository,
        EntityManagerInterface $em,
        Request $request,
        ValidatorInterface $validator
    ): JsonResponse {

        // Find user or return error
        $user = $userRepository->find($id);
        if (!$user) {
            return $this->json(["error" => "The user with ID " . $id . " does not exist"], Response::HTTP_BAD_REQUEST);
        }

        // Deserialize JSON content into object to update 
        $jsonContent = $request->getContent();
        $updatedUser = $serializer->deserialize($jsonContent, User::class, 'json', [
            AbstractNormalizer::OBJECT_TO_POPULATE => $user
        ]);

        // Validate user or return validation errors
        $validationErrors = $validator->validate($updatedUser);
        if (count($validationErrors) > 0) {
            return $this->json($validationErrors, Response::HTTP_BAD_REQUEST);
        }

        // Update property "updated_at" and save changes into database
        $updatedUser->setUpdatedAt(new DateTimeImmutable());
        $em->flush();

        // Return json with updated user datas 
        return $this->json($updatedUser, Response::HTTP_OK, [], ["groups" => "users"]);
    }



    //! DELETE USER
    /**
     * @Route("/api/users/{id}", name="app_api_user_deleteUser", methods={"DELETE"})
     * Delete one user
     */
    public function deleteUser(int $id, UserRepository $userRepository, EntityManagerInterface $em): JsonResponse
    {
        // Find user or return error
        $user = $userRepository->find($id);
        if (!$user) {
            return $this->json(["error" => "The user with ID " . $id . " does not exist"], Response::HTTP_BAD_REQUEST);
        }

        // Remove user and save changes into database or return error
        try {
            $em->remove($user);
            $em->flush();
        } catch (ORMInvalidArgumentException $e) {
            return $this->json(["error" => "Failed to delete the user with ID " . $id . ""], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
        // return json with success custom message
        return $this->json("The user with ID " . $id . " has been deleted successfully", Response::HTTP_OK);
    }




    //! GET FAVORITES USER

    /**
     * @Route("/api/users/{id}/favorites", name="app_api_user_getFavoriteUser", methods={"GET"})
     * Retrieve all favorites of a user
     */
    public function getFavoritesUser(int $id, UserRepository $userRepository): JsonResponse
    {
        // Find user or return error
        $user = $userRepository->find($id);
        if (!$user) {
            return $this->json(["error" => "The user with ID " . $id . " does not exist"], Response::HTTP_BAD_REQUEST);
        }
        // Get user's favorites or return error if the user has no favorites
        $favorites = $user->getFavorites();
        if ($favorites->isEmpty()) {
            return $this->json(["error" => "The user with ID " . $id . " has no favorites."], Response::HTTP_BAD_REQUEST);
        }

        return $this->json($favorites, Response::HTTP_OK, [], ["groups" => "userfavorites"]);
    }


    //! POST FAVORITE


    /**
     * @Route("/api/users/{id}/gardens/{gardenId}/favorites", name="app_api_user_postFavoriteUser", methods={"POST"})
     * Add a favorite garden to a user
     */

    public function postFavorite(int $id, int $gardenId, UserRepository $userRepository, GardenRepository $gardenRepository, Request $request, SerializerInterface $serializer, ValidatorInterface $validator, EntityManagerInterface $entityManager): JsonResponse
    {
        // Find user or return error
        $user = $userRepository->find($id);
        if (!$user) {
            return $this->json(["error" => "The user with ID " . $id . " does not exist"], Response::HTTP_BAD_REQUEST);
        }

        // Find garden or return error
        $garden = $gardenRepository->find($gardenId);
        if (!$garden) {
            return $this->json(["error" => "The garden with ID " . $gardenId . " does not exist"], Response::HTTP_BAD_REQUEST);
        }

        // Creating a new Favorite object instance
        $favorite = new Favorite();
        // Setting the associated garden for the favorite
        $favorite->setGarden($garden);
        // Adding the favorite to the user
        $user->addFavorite($favorite);
        // Save changes into database
        $entityManager->persist($user);
        $entityManager->flush();

        return $this->json([$user], Response::HTTP_CREATED, [
            "Location" => $this->generateUrl("app_api_user_getUsersById", ["id" => $user->getId()])
        ], [
                "groups" => "users"
            ]);
    }


    //! DELETE FAVORITE USER


    /**
     * @Route("/api/users/favorites/{id}", name="app_api_user_deleteFavoriteById", methods={"DELETE"})
     * Delete one favorite of a user
     */
    public function deleteFavoriteById(int $id, FavoriteRepository $favoriteRepository, EntityManagerInterface $em): JsonResponse
    {
        // Find favorite or return error
        $favorite = $favoriteRepository->find($id);
        if (!$favorite) {
            return $this->json(["error" => "The favorite with ID " . $id . " does not exist"], Response::HTTP_BAD_REQUEST);
        }

        // Remove favorite
        $em->remove($favorite);

        // Save changes into database 
        $em->flush();

        return $this->json("the favorite " . $id . " has been deleted with success", Response::HTTP_OK);


    }


    //! DELETE FAVORITES USER

    /**
     * @Route("/api/users/{id}/favorites", name="app_api_user_deleteFavorites", methods={"DELETE"})
     * Delete all favorites of a user
     */
    public function deleteFavorites(int $id, UserRepository $userRepository, FavoriteRepository $favoriteRepository, EntityManagerInterface $em): JsonResponse
    {
        // Find user or return error
        $user = $userRepository->find($id);
        if (!$user) {
            return $this->json(["error" => "The user with ID " . $id . " has no favorites."], Response::HTTP_BAD_REQUEST);
        }

        // Retrieve all the favorites With findBy() method
        $favorites = $favoriteRepository->findBy(["user" => $id]);
        // Early return si l'utilisateur n'a pas de favoris
        if (!$favorites) {
            return $this->json(["error" => "The user with ID " . $id . " has no favorites."], Response::HTTP_BAD_REQUEST);
        }

        // Delete all the favorites one by one
        foreach ($favorites as $favorite) {
            $em->remove($favorite);
        }
        // Save changes into database 
        $em->flush();

        return $this->json("all favorites of user with ID " . $id . "  have been deleted with success", Response::HTTP_OK);


    }

    //! GET GARDENS USER


    /**
     * @Route("/api/users/{id}/gardens", name="app_api_user_getGardensUser", methods={"GET"})
     * Retrieve all the gardens of a user
     */
    public function getGardensUser(int $id, UserRepository $userRepository): JsonResponse
    {
        // Find user or return error
        $user = $userRepository->find($id);
        if (!$user) {
            return $this->json(["error" => "The user with ID " . $id . " does not exist"], Response::HTTP_BAD_REQUEST);
        }

        // Retrieve all the gardens of a user
        $gardens = $user->getGardens();

        return $this->json($gardens, Response::HTTP_OK, [], ["groups" => "gardensUser"]);
    }




}