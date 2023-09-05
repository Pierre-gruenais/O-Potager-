<?php

namespace App\Controller\Back;

use App\Entity\User;
use App\Form\UserType;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;



/**
 * @Route("/admin/utilisateurs")
 * Retrieve a list of users
 */
class UserController extends AbstractController
{
    /**
     * @Route("/", name="app_back_user_list", methods={"GET"})
     */
    public function list(UserRepository $userRepository): Response
    {
        return $this->render('back/user/list.html.twig', [
            'users' => $userRepository->findAll(),
        ]);
    }


    /**
     * @Route("/{id}", name="app_back_user_show", methods={"GET"}, requirements={"id"="\d+"})
     * Retrieve datas of a user
     */
    public function show(User $user): Response
    {
        return $this->render('back/user/show.html.twig', [
            'user' => $user,
        ]);
    }

    /**
     * @Route("/modifier/{id}", name="app_back_user_edit", methods={"GET", "POST"}, requirements={"id"="\d+"})
     * Update user datas
     */
    public function edit(Request $request, User $user, UserRepository $userRepository): Response
    {
        $form = $this->createForm(UserType::class, $user, ["custom_option" => "edit"]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $user->setUpdatedAt(new \DateTimeImmutable);

            $userRepository->add($user, true);
            $this->addFlash("success", "Modifications sur l'utilisateur ".$user->getUsername()." bien prises en compte");
            return $this->redirectToRoute('app_back_user_list', [], Response::HTTP_SEE_OTHER);
        }


        return $this->renderForm('back/user/edit.html.twig', [
            'user' => $user,
            'form' => $form,
        ]);
    }

    /**
     * @Route("/supprimer/{id}", name="app_back_user_delete", methods={"POST"}, requirements={"id"="\d+"})
     * Delete user 
     */
    public function delete(Request $request, User $user, UserRepository $userRepository): Response
    {
        if ($this->isCsrfTokenValid('delete' . $user->getId(), $request->request->get('_token'))) {
            $userRepository->remove($user, true);
        }
        $this->addFlash("success", "L'utilisateur ".$user->getUsername()." a bien été supprimé ");

        return $this->redirectToRoute('app_back_user_list', [], Response::HTTP_SEE_OTHER);
    }

    /**
     * @Route("/ajouter", name="app_back_user_add" ,  methods={"GET", "POST"})
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
 
             $this->addFlash('success', 'Utilisateur bien ajouté');
 
             return $this->redirectToRoute('app_back_user_show', ['id' => $user->getId()]);
         }
 
         return $this->renderForm('back/user/new.html.twig', [
             'form' => $form,
         ]);
     }
}