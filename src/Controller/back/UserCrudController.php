<?php

namespace App\Controller\back;

use App\Entity\User;
use App\Form\UserType;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/admin")
 */
class UserCrudController extends AbstractController
{
    /**
     * @Route("/users", name="app_userCrud_list", methods={"GET"})
     */
    public function list(UserRepository $userRepository): Response
    {
        return $this->render('user/list.html.twig', [
            'users' => $userRepository->findAll(),
        ]);
    }

    /**
     * @Route("/new", name="app_user_new", methods={"GET", "POST"})
     */
    public function new(Request $request, UserRepository $userRepository): Response
    {
        $user = new User();

        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);
          dd($user);
        if ($form->isSubmitted() && $form->isValid()) {
          
            $userRepository->add($user, true);

            return $this->redirectToRoute('app_userCrud_list', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('user/new.html.twig', [
            'user' => $user,
            'form' => $form,
        ]);
    }

    /**
     * @Route("/{id}", name="app_user_show", methods={"GET"})
     */
    public function show(User $user): Response
    {
        return $this->render('user/show.html.twig', [
            'user' => $user,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="app_user_edit", methods={"GET", "POST"})
     */
    public function edit(Request $request, User $user, UserRepository $userRepository): Response
    {
        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);
      
        if ($form->isSubmitted() && $form->isValid()) {

            
            $userRepository->add($user, true);

            return $this->redirectToRoute('app_userCrud_list', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('user/edit.html.twig', [
            'user' => $user,
            'form' => $form,
        ]);
    }

    /**
     * @Route("/{id}", name="app_user_delete", methods={"POST"})
     */
    public function delete(Request $request, User $user, UserRepository $userRepository): Response
    {
        if ($this->isCsrfTokenValid('delete' . $user->getId(), $request->request->get('_token'))) {
            $userRepository->remove($user, true);
        }

        return $this->redirectToRoute('app_userCrud_list', [], Response::HTTP_SEE_OTHER);
    }
//! flo route a ajouter dans la branche dev sans merger la branche -->juste copie colle
 /**
     * @Route("/add", name="app_back_user_add" ,  methods={"GET", "POST"})
     * on ajoute un utilisateur
     */

     public function add(Request $request, EntityManagerInterface $em)
     {
         $user = new User();
 
         $form = $this->createForm(UserType::class, $user);
 
         $form->handleRequest($request);
 
         if ($form->isSubmitted() && $form->isValid()) {
 
             $em->persist($user);
             $em->flush();
 
             $this->addFlash('success', 'User ajouté');
 
             return $this->redirectToRoute('app_back_user_show', ['id' => $user->getId()]);
         }
 
         return $this->renderForm('faq/question/add.html.twig', [
             'form' => $form,
         ]);
     }

}