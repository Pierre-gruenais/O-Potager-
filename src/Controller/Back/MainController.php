<?php

namespace App\Controller\Back;

use App\Repository\GardenRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class MainController extends AbstractController
{
    /**
     * @Route("/admin", name="app_back_main_home")
     */
    public function home(GardenRepository $gardenRepository): Response
    {
        $gardens = $gardenRepository->findAll();
        
        return $this->render('back/main/home.html.twig', [
            'gardens' => $gardens
        ]);
    }
}
