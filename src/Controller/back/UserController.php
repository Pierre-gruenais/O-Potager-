<?php

namespace App\Controller\back;

use App\Entity\User;
use App\Form\UserType;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;



/**
 * @Route("/admin/users")
 */
class UserController extends AbstractController
{
    /**
     * @Route("/list", name="app_back_user_list", methods={"GET"})
     */
    public function list(UserRepository $userRepository): Response
    {
        return $this->render('back/user/list.html.twig', [
            'users' => $userRepository->findAll(),
        ]);
    }



    /**
     * @Route("/{id}", name="app_back_user_show", methods={"GET"}, requirements={"id"="\d+"})
     */
    public function show(User $user): Response
    {
        return $this->render('back/user/show.html.twig', [
            'user' => $user,
        ]);
    }

    /**
     * @Route("/edit/{id}", name="app_back_user_edit", methods={"GET", "POST"}, requirements={"id"="\d+"})
     */
    public function edit(Request $request, User $user, UserRepository $userRepository): Response
    {
        $form = $this->createForm(UserType::class, $user, ["custom_option" => "edit"]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $user->setUpdatedAt(new \DateTimeImmutable);

            $userRepository->add($user, true);

            return $this->redirectToRoute('app_back_user_list', [], Response::HTTP_SEE_OTHER);
        }


        return $this->renderForm('back/user/edit.html.twig', [
            'user' => $user,
            'form' => $form,
        ]);
    }

    /**
     * @Route("/delete/{id}", name="app_back_user_delete", methods={"POST"}, requirements={"id"="\d+"})
     */
    public function delete(Request $request, User $user, UserRepository $userRepository): Response
    {
        if ($this->isCsrfTokenValid('delete' . $user->getId(), $request->request->get('_token'))) {
            $userRepository->remove($user, true);
        }

        return $this->redirectToRoute('app_back_user_list', [], Response::HTTP_SEE_OTHER);
    }
}