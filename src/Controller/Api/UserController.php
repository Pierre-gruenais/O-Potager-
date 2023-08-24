<?php

namespace App\Controller\Api;

use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Response;

class UserController extends AbstractController
{
    /**
     * @Route("/api/users", name="app_api_user_getUsers")
     */
    public function getUsers(UserRepository $userRepository): JsonResponse
    {
        $users = $userRepository->findAll();

        return $this->json($users, Response::HTTP_OK, [], ["groups" => "users"]);
    }
    
   /**
     * @Route("/api/users/{id}", name="app_api_user_getUsersById")
     */
    public function getUsersById(int $id, UserRepository $userRepository): JsonResponse
    {

        $user = $userRepository->find($id);
            // ! potentiellement j'ai une erreur si le film n'existe pas
        if (!$user) {
            return $this->json(["error" => "l'utisateur n'esxiste pas"], Response::HTTP_BAD_REQUEST);
        }
        return $this->json($user, Response::HTTP_OK, [], ["groups" => "users"]);
    }
}
