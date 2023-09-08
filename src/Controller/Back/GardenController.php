<?php

namespace App\Controller\Back;

use App\Entity\Garden;
use App\Entity\Picture;
use App\Form\GardenType;
use App\Repository\GardenRepository;
use App\Repository\PictureRepository;
use App\Service\MyMailerService;
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
     */
    public function __construct(NominatimApiService $nominatimApi)
    {
        $this->nominatimApi = $nominatimApi;
    }

    /**
     * route for retrieving all garden data
     * 
     * @Route("/", name="app_back_garden_list", methods={"GET"})
     *
     * @param GardenRepository $gardenRepository
     * @return Response
     */
    public function list(GardenRepository $gardenRepository): Response
    {
        return $this->render('back/garden/list.html.twig', [
            'gardens' => $gardenRepository->findAll(),
        ]);
    }

    /**
     * route to retrieve all data for a garden
     * 
     * @Route("/{id}", name="app_back_garden_show", methods={"GET"})
     *
     * @param Garden $garden id of the garden
     * @return Response
     */
    public function show(Garden $garden): Response
    {
        return $this->render('back/garden/show.html.twig', [
            'garden' => $garden,
        ]);
    }

    /**
     * path to update a garden
     * 
     * @Route("/modifier/{id}", name="app_back_garden_edit", methods={"GET", "POST"})
     *
     * @param Request $request
     * @param Garden $garden id of the garden
     * @param GardenRepository $gardenRepository
     * @return Response
     */
    public function edit(Request $request, Garden $garden, GardenRepository $gardenRepository, MyMailerService $mailer): Response
    {
        $form = $this->createForm(GardenType::class, $garden);
        $form->handleRequest($request);

        $coordinatesCityApi = $this->nominatimApi->getCoordinates($garden->getCity(), $garden->getAddress());

        if (!$coordinatesCityApi) {
            $this->addFlash("warning", "L'adresse est introuvable.");
            return $this->renderForm('back/garden/edit.html.twig', [
                'garden' => $garden,
                'form'   => $form,
            ]);
        }
        $garden->setLat($coordinatesCityApi[ 'lat' ]);
        $garden->setLon($coordinatesCityApi[ 'lon' ]);

        if ($form->isSubmitted() && $form->isValid()) {

            $garden->setUpdatedAt(new DateTimeImmutable());

            $gardenRepository->add($garden, true);
            $this->addFlash("success", "Les modifications du jardin ont bien été prises en compte.");

            // envoi d'un email apres moderation par la validation de l'edit
            $to = $garden->getUser()->getEmail();

            $mailer->send("validation de votre jardin", "emails/moderation.html.twig",["garden" => $garden] , $to);
            $this->addFlash("success", "Un email avertissant de l'acceptation du jardin par nos moderateur a bien été envoyé.");

            return $this->redirectToRoute('app_back_garden_list',[], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('back/garden/edit.html.twig', [
            'garden' => $garden,
            'form'   => $form,
        ]);
    }

    /**
     * Delete a garden by id
     * 
     * @Route("/supprimer/{id}", name="app_back_garden_delete", methods={"POST"})
     *
     * @param Request $request
     * @param Garden $garden id of the garden
     * @param GardenRepository $gardenRepository
     * @return Response
     */
    public function delete(Request $request, Garden $garden, GardenRepository $gardenRepository): Response
    {
        if ($this->isCsrfTokenValid('delete' . $garden->getId(), $request->request->get('_token'))) {
            $gardenRepository->remove($garden, true);
        }
        $this->addFlash("success", "Le jardin a bien été supprimé.");

        return $this->redirectToRoute('app_back_garden_list', [], Response::HTTP_SEE_OTHER);
    }

    /**
     * delete picture by id
     * 
     * @Route("/images/{id}", name="app_back_garden_deletePicture", methods={"POST"})
     *
     * @param Request $request
     * @param Picture $picture id picture
     * @param PictureRepository $pictureRepository
     * @return Response
     */
    public function deletePicture(Request $request, Picture $picture, PictureRepository $pictureRepository): Response
    {
        if ($this->isCsrfTokenValid('delete'.$picture->getId(), $request->request->get('_token'))) {
            $pictureRepository->remove($picture, true);
        }
        $this->addFlash("success", "La photo a bien été supprimée.");
        
        $referer = $request->headers->get("referer");
        
        return $this->redirect($referer);
    }
}
