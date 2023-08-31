<?php

namespace App\Controller\Api;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use ImageKit\ImageKit;
use Symfony\Component\HttpFoundation\Response;

class ImageKitAuthController extends AbstractController
{
    /**
     * route for authenticating to the ImageKit API
     * 
     * @Route("/imagekit/auth", name="app_api_imageKitAuth_authenticateImageKit", methods={"GET"})
     * 
     * @return JsonResponse
     */
    public function authenticateImageKit(): JsonResponse
    {
        $publicKey = "public_mOe2SQYQyudsmyQ9EFcbH0SqgEk=";
        $privateKey = "private_O/Ki3ys61OuoXazE9hCHHB3s28s=";
        $urlEndPoint = "https://ik.imagekit.io/opotager/";

        $imageKit = new ImageKit(
            $publicKey,
            $privateKey,
            $urlEndPoint
        );

        $authenticationParameters = $imageKit->getAuthenticationParameters();
    
        return $this->json($authenticationParameters, Response::HTTP_OK);
    }
}
