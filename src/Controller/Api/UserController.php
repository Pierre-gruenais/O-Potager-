<?php

namespace App\Controller\Api;

use App\Entity\User;
use App\Entity\Garden;
use App\Repository\FavoriteRepository;

use App\Repository\UserRepository;
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
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;

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
    public function putUser(int $id, SerializerInterface $serializer, userRepository $userRepository, EntityManagerInterface $em, Request $request, ValidatorInterface $validator): JsonResponse
    {
        $user = $userRepository->find($id);
        // potentiellement j'ai une erreur si l'utilisateur' n'existe pas
        if (!$user) {
            return $this->json(["error" => "l'utilisateur n'existe pas"], Response::HTTP_BAD_REQUEST);
        }

        $jsonContent = $request->getContent();
        // potentiellement j'ai une erreur si le json n'est pas bon
        try {
            $updatedUser = $serializer->deserialize($jsonContent, User::class, 'json', [AbstractNormalizer::OBJECT_TO_POPULATE => $user]);

        } catch (NotEncodableValueException $e) {

            return $this->json(["error" => "JSON INVALID"], Response::HTTP_BAD_REQUEST);
        }


        $errors = $validator->validate($updatedUser);
        if (count($errors) > 0) {
            return $this->json($errors, 400);
        }
        $em->persist($updatedUser);
        $em->flush();

        return $this->json($updatedUser, Response::HTTP_OK, [], ["groups" => "users"]);
    }


    //! DELETE USER
    /**
     * @Route("/api/users/{id}", name="app_api_user_deleteUser", methods={"delete"})
     */
    public function deleteUser(int $id, userRepository $userRepository, EntityManagerInterface $em): JsonResponse
    {
        $user = $userRepository->find($id);
        // potentiellement j'ai une erreur si l'utilisateur' n'existe pas
        try {
            $em->remove($user);
        } catch (ORMInvalidArgumentException $e) {

            return $this->json(["error" => "l'utilisateur' n'existe pas"], Response::HTTP_BAD_REQUEST);
        }
        $em->flush();
        return $this->json("the user has been deleted with success", Response::HTTP_OK);
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

    //! GET GARDEN USER

    //! GET USERS

    //! GET FAVORITE USER

    /**
     * @Route("/api/users/{id}/favorites", name="app_api_user_getFavoriteUser", methods={"GET"})
     * on recupere tous les favoris d'un utilisateur
     */
    public function getFavoriteUser(int $id, UserRepository $userRepository): JsonResponse
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

        return $this->json($favorites, Response::HTTP_OK, [], ["groups" => "userWithRelations"]);
    }

    //! DELETE FAVORITE USER


    /**
     * @Route("/api/users/favorites/{id}", name="app_api_user_deleteFavoriteById", methods={"DELETE"})
     * on supprime un favoris d'un utilisateur
     */
    public function deleteFavoriteById(int $id, FavoriteRepository $favoriteRepository, EntityManagerInterface $em): JsonResponse
    {
        //  potentiellement j'ai une erreur si le favoris n'existe pas
        // on recupere le favoris
        $favorite = $favoriteRepository->find($id);

        try {
            $em->remove($favorite);
        } catch (ORMInvalidArgumentException $e) {

            return $this->json(["error" => "le favoris n'existe pas"], Response::HTTP_BAD_REQUEST);
        }
        $em->flush();
        return $this->json("the favorite has been deleted with success", Response::HTTP_OK);


    }

    //! DELETE FAVORITE-id USER 

    //! DELETE FAVORITES USER

    //! DELETE FAVORITE USER


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
        // on recupere tous les favoris
        $favorites = $favoriteRepository->findAllFavoritesByUserId($id);


        foreach ($favorites as $favorite) {
            $em->remove($favorite);
        }

        $em->flush();

        $username = $user->getUsername();
        return $this->json("all favorites of $username  have been deleted with success", Response::HTTP_OK);


    }

}