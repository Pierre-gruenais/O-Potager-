<?php

namespace App\Controller\Back;

use App\Entity\Garden;
use App\Form\GardenType;
use App\Repository\GardenRepository;
use App\Service\NominatimApiService;
use DateTimeImmutable;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/admin/jardins")
 */
class GardenController extends AbstractController
{
    private $nominatimApi;

    /**
     * Construct of the class
     *
     * @param NominatimApiService $nominatimApi NominatimAPI call service
     * @param ValidatorErrorService $validatorError ValidatorError call service
     */
    public function __construct(NominatimApiService $nominatimApi)
    {
        $this->nominatimApi = $nominatimApi;
    }

    /**
     * @Route("/", name="app_back_garden_list", methods={"GET"})
     */
    public function list(GardenRepository $gardenRepository): Response
    {
        return $this->render('back/garden/list.html.twig', [
            'gardens' => $gardenRepository->findAll(),
        ]);
    }

    /**
     * @Route("/{id}", name="app_back_garden_show", methods={"GET"})
     */
    public function show(Garden $garden): Response
    {
        return $this->render('back/garden/show.html.twig', [
            'garden' => $garden,
        ]);
    }

    /**
     * @Route("/{id}/modifier", name="app_back_garden_edit", methods={"GET", "POST"})
     */
    public function edit(Request $request, Garden $garden, GardenRepository $gardenRepository): Response
    {
        $form = $this->createForm(GardenType::class, $garden);
        $form->handleRequest($request);
        
        $coordinatesCityApi = $this->nominatimApi->getCoordinates($garden->getCity(), $garden->getAddress());
        
        if(!$coordinatesCityApi){
            $this->addFlash("warning", "L'adresse est introuvable.");
            return $this->renderForm('back/garden/edit.html.twig', [
                'garden' => $garden,
                'form' => $form,
            ]);
        }
        $garden->setLat($coordinatesCityApi['lat']);
        $garden->setLon($coordinatesCityApi['lon']);

        if ($form->isSubmitted() && $form->isValid()) {

            $garden->setUpdatedAt(new DateTimeImmutable());
            
            $gardenRepository->add($garden, true);
            $this->addFlash("success", "Les modifications du jardin ont bien été prises en compte.");
            return $this->redirectToRoute('app_back_garden_list', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('back/garden/edit.html.twig', [
            'garden' => $garden,
            'form' => $form,
        ]);
    }

    /**
     * @Route("/{id}", name="app_back_garden_delete", methods={"POST"})
     */
    public function delete(Request $request, Garden $garden, GardenRepository $gardenRepository): Response
    {
        if ($this->isCsrfTokenValid('delete'.$garden->getId(), $request->request->get('_token'))) {
            $gardenRepository->remove($garden, true);
        }
        $this->addFlash("success", "Le jardin a bien été supprimé.");
        
        return $this->redirectToRoute('app_back_garden_list', [], Response::HTTP_SEE_OTHER);
    }
}
