<?php

namespace App\Controller\Back;

use App\Repository\GardenRepository;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class MainController extends AbstractController
{
    /**
     * route to home page with data 
     * 
     * @Route("/admin", name="app_back_main_home")
     *
     * @param GardenRepository $gardenRepository
     * @param UserRepository $userRepository
     * @return Response
     */
    public function home(GardenRepository $gardenRepository, UserRepository $userRepository): Response
    {
        $gardens = $gardenRepository->findByCheckedField("En cours");
        $users = $userRepository->findFiveByAdd();
        
        return $this->render('back/main/home.html.twig', [
            'gardens' => $gardens,
            'users' => $users
        ]);
    }
}
