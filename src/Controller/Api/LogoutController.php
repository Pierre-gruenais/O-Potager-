<?php

namespace App\Controller\Api;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

class LogoutController extends AbstractController
{
    
    private $tokenStorage;

    public function __construct( TokenStorageInterface $tokenStorage)
    {
        $this->tokenStorage = $tokenStorage;
    }

    /**
     * @Route("/api/logout", name="app_api_logout_logoutApi", methods={"POST"})
     */
    public function logoutApi()
    {
        // Récupérer le token JWT de l'utilisateur actuellement authentifié
        $token = $this->tokenStorage->getToken();

        if ($token) {
           
            // Déconnexion de l'utilisateur
            $this->tokenStorage->setToken(null);
        }

       return $this->json('Déconnexion réussie', Response::HTTP_OK);
    }
}
