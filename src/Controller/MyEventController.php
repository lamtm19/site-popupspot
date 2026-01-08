<?php

namespace App\Controller;

use App\Entity\Event;
use App\Form\EventType;
use App\Repository\EventRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class MyEventController extends AbstractController
{
    public function index(EventRepository $er): Response
    {
        $this->denyAccessUnlessGranted('ROLE_USER');

        $events = $er->findBy(
            ['author' => $this->getUser()],
            ['eventDate' => 'ASC']
        );

        return $this->render('my_event/index.html.twig', [
            'events' => $events,
        ]);
    }

    public function edit(Event $event, Request $request, EntityManagerInterface $em): Response
    {
        $this->denyAccessUnlessGranted('ROLE_USER');

        // sécurité : seul l’auteur peut modifier
        if ($event->getAuthor()->getId() !== $this->getUser()->getId()) {
            throw $this->createAccessDeniedException();
        }

        $form = $this->createForm(EventType::class, $event);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            // Toute modif doit repasser en validation admin
            $event->setIsValidated(false);

            $em->flush();
            $this->addFlash('success', 'Événement modifié. Il repasse en validation admin.');

            return $this->redirectToRoute('app_my_events');
        }

        return $this->render('my_event/edit.html.twig', [
            'event' => $event,
            'form' => $form->createView(),
        ]);
    }

    public function delete(Event $event, EntityManagerInterface $em): Response
    {
        $this->denyAccessUnlessGranted('ROLE_USER');

        // sécurité : seul l’auteur peut supprimer
        if ($event->getAuthor()->getId() !== $this->getUser()->getId()) {
            throw $this->createAccessDeniedException();
        }

        $em->remove($event);
        $em->flush();

        $this->addFlash('success', 'Événement supprimé.');

        return $this->redirectToRoute('app_my_events');
    }
}
