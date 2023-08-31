<?php

namespace App\Controller\Back;

use App\Entity\Garden;
use App\Form\GardenType;
use App\Repository\GardenRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/back/garden")
 */
class GardenController extends AbstractController
{
    /**
     * @Route("/", name="app_back_garden_index", methods={"GET"})
     */
    public function index(GardenRepository $gardenRepository): Response
    {
        return $this->render('back/garden/index.html.twig', [
            'gardens' => $gardenRepository->findAll(),
        ]);
    }

    /**
     * @Route("/new", name="app_back_garden_new", methods={"GET", "POST"})
     */
    public function new(Request $request, GardenRepository $gardenRepository): Response
    {
        $garden = new Garden();
        $form = $this->createForm(GardenType::class, $garden);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $gardenRepository->add($garden, true);

            return $this->redirectToRoute('app_back_garden_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('back/garden/new.html.twig', [
            'garden' => $garden,
            'form' => $form,
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
     * @Route("/{id}/edit", name="app_back_garden_edit", methods={"GET", "POST"})
     */
    public function edit(Request $request, Garden $garden, GardenRepository $gardenRepository): Response
    {
        $form = $this->createForm(GardenType::class, $garden);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $gardenRepository->add($garden, true);

            return $this->redirectToRoute('app_back_garden_index', [], Response::HTTP_SEE_OTHER);
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

        return $this->redirectToRoute('app_back_garden_index', [], Response::HTTP_SEE_OTHER);
    }
}
