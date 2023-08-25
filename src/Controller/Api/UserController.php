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
     */
    public function getUsers(UserRepository $userRepository): JsonResponse
    {
        $users = $userRepository->findAll();

        return $this->json($users, Response::HTTP_OK, [], ["groups" => "users"]);
    }



    //! GET USER
    /**
     * @Route("/api/users/{id}", name="app_api_user_getUsersById", methods={"GET"})
     */
    public function getUsersById(int $id, UserRepository $userRepository): JsonResponse
    {

        $user = $userRepository->find($id);
        //  potentiellement j'ai une erreur si l'utilisateur n'existe pas
        if (!$user) {
            return $this->json(["error" => "l'utisateur n'existe pas"], Response::HTTP_BAD_REQUEST);
        }
        return $this->json($user, Response::HTTP_OK, [], ["groups" => "users"]);
    }



    //! POST USER
    /**
     * @Route("/api/users", name="app_api_user_postUsers", methods={"POST"})
     */
    public function postUsers(Request $request, SerializerInterface $serializer, ValidatorInterface $validator, EntityManagerInterface $entityManager): JsonResponse
    {
        // je récupere un json en brut
        $jsonContent = $request->getContent();
        //! verifier si l'utilisateur existe deja
        // potentiellement j'ai une erreur si le json n'est pas bon
        // je transforme ce json en entité user
        try {
            $user = $serializer->deserialize($jsonContent, User::class, 'json');
        } catch (NotEncodableValueException $e) {
            // je gere le cas ou il ya l'erreur
            return $this->json(["error" => "JSON INVALID"], Response::HTTP_BAD_REQUEST);
        }



        // je detecte les erreurs sur mon entité avant de la persister
        $errors = $validator->validate($user);

        // on renvoi un json avec les erreurs
        if (count($errors) > 0) {

            // je crée un nouveau tableau d'erreur
            $dataErrors = [];

            foreach ($errors as $error) {
                // j'injecte dans le tableau à l'index de l'input, les messages d'erreurs qui concernent l'erreur en question
                $dataErrors[$error->getPropertyPath()][] = $error->getMessage();
            }

            // je retourne le json avec mes erreurs
            return $this->json($dataErrors, Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        $entityManager->persist($user);

        $entityManager->flush();

        return $this->json([$user], Response::HTTP_CREATED, [
            "Location" => $this->generateUrl("app_api_user_getUsersById", ["id" => $user->getId()])
        ], [
                "groups" => "users"
            ]);
    }


    //! PUT USER
    /**
     * @Route("/api/users/{id}", name="app_api_user_putUser", methods={"PUT"})
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
            return $this->json(["error" => "User does not exist"], Response::HTTP_BAD_REQUEST);
        }

        // Deserialize JSON content or return error
        $jsonContent = $request->getContent();
        try {
            $updatedUser = $serializer->deserialize($jsonContent, User::class, 'json', [
                AbstractNormalizer::OBJECT_TO_POPULATE => $user
            ]);
        } catch (NotEncodableValueException $e) {
            return $this->json(["error" => "Invalid JSON"], Response::HTTP_BAD_REQUEST);
        }

        // Validate user or return validation errors
        $validationErrors = $validator->validate($updatedUser);
        if (count($validationErrors) > 0) {
            return $this->json($validationErrors, Response::HTTP_BAD_REQUEST);
        }

        // Update user and save changes
        $updatedUser->setUpdatedAt(new DateTimeImmutable());
        $em->flush();

        return $this->json($updatedUser, Response::HTTP_OK, [], ["groups" => "users"]);
    }



    //! DELETE USER
    /**
     * @Route("/api/users/{id}", name="app_api_user_deleteUser", methods={"DELETE"})
     */
    public function deleteUser(int $id, UserRepository $userRepository, EntityManagerInterface $em): JsonResponse
    {
        $user = $userRepository->find($id);

        if (!$user) {
            return $this->json(["error" => "User does not exist"], Response::HTTP_BAD_REQUEST);
        }

        try {
            $em->remove($user);
            $em->flush();
        } catch (ORMInvalidArgumentException $e) {
            return $this->json(["error" => "Failed to delete user"], Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        return $this->json("The user has been deleted successfully", Response::HTTP_OK);
    }







    //! GET FAVORITES USER

    /**
     * @Route("/api/users/{id}/favorites", name="app_api_user_getFavoriteUser", methods={"GET"})
     * on recupere tous les favoris d'un utilisateur
     */
    public function getFavoritesUser(int $id, UserRepository $userRepository): JsonResponse
    {
        // on recupere l'utilisateur
        $user = $userRepository->find($id);
        //  potentiellement j'ai une erreur si l'utilisateur n'existe pas
        if (!$user) {
            return $this->json(["error" => "l'utisateur n'existe pas"], Response::HTTP_BAD_REQUEST);
        }
        // on recupere les favoris de l'utisateur
        $favorites = $user->getFavorites();
        // si l'utilisateur n'a pas de favoris on retourne une erreur
        if ($favorites->isEmpty()) {
            return $this->json(["error" => "l'utisateur n'a pas de favoris"], Response::HTTP_BAD_REQUEST);
        }

        return $this->json($favorites, Response::HTTP_OK, [], ["groups" => "userfavorites"]);
    }


    //! POST FAVORITE


    /**
     * @Route("/api/users/{id}/gardens/{gardenId}/favorites", name="app_api_user_postFavoriteUser", methods={"POST"})
     * on ajoute un favori a un utilisateur
     */

    public function postFavorite(int $id,int $gardenId, UserRepository $userRepository,GardenRepository $gardenRepository, Request $request, SerializerInterface $serializer, ValidatorInterface $validator, EntityManagerInterface $entityManager): JsonResponse
    {
        // on recupere l'utilisateur
        $user = $userRepository->find($id);
        //  potentiellement j'ai une erreur si l'utilisateur n'existe pas
        if (!$user) {
            return $this->json(["error" => "l'utisateur n'existe pas"], Response::HTTP_BAD_REQUEST);
        }
        $garden = $gardenRepository->find($gardenId);
         //  potentiellement j'ai une erreur si le jardin n'existe pas
         if (!$garden) {
            return $this->json(["error" => "le jardin n'existe pas"], Response::HTTP_BAD_REQUEST);
        }
        $favorite = new Favorite();
        $favorite->setGarden($garden);
        $user->addFavorite($favorite);
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
     * @Route("/api/users/{id}/favorites/{favoriteId}", name="app_api_user_deleteFavoriteById", methods={"DELETE"})
     * on supprime un favoris d'un utilisateur
     */
    public function deleteFavoriteById(int $id, int $favoriteId, FavoriteRepository $favoriteRepository, EntityManagerInterface $em): JsonResponse
    {
        //  potentiellement j'ai une erreur si le favoris n'existe pas
        // on recupere le favoris
        $favorites = $favoriteRepository->findOneFavoritesByUserId($id, $favoriteId);


        try {
            foreach ($favorites as $favorite) {
                $em->remove($favorite);
            }
        } catch (ORMInvalidArgumentException $e) {

            return $this->json(["error" => "le favoris n'existe pas"], Response::HTTP_BAD_REQUEST);
        }
        $em->flush();
        return $this->json("the favorite has been deleted with success", Response::HTTP_OK);


    }


    //! DELETE FAVORITES USER

    /**
     * @Route("/api/users/{id}/favorites", name="app_api_user_deleteFavorites", methods={"DELETE"})
     * on supprime tous les favoris d'un utilisateur
     */
    public function deleteFavorites(int $id, UserRepository $userRepository, FavoriteRepository $favoriteRepository, EntityManagerInterface $em): JsonResponse
    {
        // on recupere l'utilisateur
        $user = $userRepository->find($id);

        //  potentiellement j'ai une erreur si l'utilisateur n'existe pas
        if (!$user) {
            return $this->json(["error" => "l'utisateur n'existe pas"], Response::HTTP_BAD_REQUEST);
        }
        //! is empty
        // on recupere tous les favoris
        $favorites = $favoriteRepository->findAllFavoritesByUserId($id);


        foreach ($favorites as $favorite) {
            $em->remove($favorite);
        }

        $em->flush();

        $username = $user->getUsername();
        return $this->json("all favorites of $username  have been deleted with success", Response::HTTP_OK);


    }

    //! GET GARDENS USER


    /**
     * @Route("/api/users/{id}/gardens", name="app_api_user_getGardensUser", methods={"GET"})
     * récupérer tous les jardins d'un utilisateur
     */
    public function getGardensUser(int $id, UserRepository $userRepository): JsonResponse
    {

        $user = $userRepository->find($id);
        //  potentiellement j'ai une erreur si l'utilisateur n'existe pas
        if (!$user) {
            return $this->json(["error" => "l'utisateur n'existe pas"], Response::HTTP_BAD_REQUEST);
        }
        $gardens = $user->getGardens();

        return $this->json($gardens, Response::HTTP_OK, [], ["groups" => "gardensUser"]);
    }




}