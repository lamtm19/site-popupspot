<?php

namespace App\Controller;

use App\Form\ProfileType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;




class ProfileController extends AbstractController
{
    public function index(): Response
    {
        $this->denyAccessUnlessGranted('ROLE_USER');

        return $this->render('profile/index.html.twig');
    }

    public function edit(Request $request, EntityManagerInterface $em): Response
    {
        $this->denyAccessUnlessGranted('ROLE_USER');

        $user = $this->getUser();

        $form = $this->createForm(ProfileType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->flush();
            $this->addFlash('success', 'Profil mis à jour ✅');
            return $this->redirectToRoute('app_profile');
        }

        return $this->render('profile/edit.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    public function delete(
        Request $request,
        EntityManagerInterface $em,
        TokenStorageInterface $tokenStorage
    ): Response {
        $this->denyAccessUnlessGranted('ROLE_USER');

        $user = $this->getUser();

        // 1) Déconnecter vraiment (vider le token)
        $tokenStorage->setToken(null);

        // 2) Détruire la session
        $request->getSession()->invalidate();

        // 3) Supprimer l'utilisateur
        $em->remove($user);
        $em->flush();

        // 4) Redirection
        return $this->redirectToRoute('app_home');
    }


}
