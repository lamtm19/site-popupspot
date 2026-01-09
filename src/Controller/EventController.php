<?php

namespace App\Controller;

use App\Entity\Event;
use App\Form\EventType;
use App\Repository\EventRepository;
use DateTimeImmutable;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class EventController extends AbstractController
{
    public function index(EventRepository $er): Response
    {
        // On affiche uniquement les events VALIDÉS + futurs
        $events = $er->createQueryBuilder('e')
            ->andWhere('e.isValidated = :ok')
            ->andWhere('e.eventDate >= :now')
            ->setParameter('ok', true)
            ->setParameter('now', new \DateTimeImmutable())
            ->orderBy('e.eventDate', 'ASC')
            ->getQuery()
            ->getResult();

        return $this->render('event/index.html.twig', [
            'events' => $events,
        ]);
    }

    public function show(Event $event): Response
    {
        // Si pas validé, on bloque sauf si admin ou auteur
        if (!$event->isIsValidated()) {
            $user = $this->getUser();

            $isAuthor = $user && method_exists($event, 'getAuthor') && $event->getAuthor() && $event->getAuthor()->getId() === $user->getId();
            $isAdmin = $this->isGranted('ROLE_ADMIN');

            if (!$isAdmin && !$isAuthor) {
                throw $this->createNotFoundException();
            }
        }

        return $this->render('event/show.html.twig', [
            'event' => $event,
        ]);
    }

    public function new(Request $request, EntityManagerInterface $em): Response
    {
        $this->denyAccessUnlessGranted('ROLE_USER');

        $event = new Event();
        $form = $this->createForm(EventType::class, $event);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // auteur + en attente de validation
            $event->setAuthor($this->getUser());
            $event->setIsValidated(false);

            $em->persist($event);
            $em->flush();

            $this->addFlash('success', 'Événement envoyé ! Il sera visible après validation.');

            return $this->redirectToRoute('app_home');
        }

        return $this->render('event/new.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
