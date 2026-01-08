<?php

namespace App\Controller;

use App\Entity\Event;
use App\Repository\EventRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;

class AdminEventController extends AbstractController
{
    public function pending(EventRepository $er): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        $events = $er->findBy(
            ['isValidated' => false],
            ['eventDate' => 'ASC']
        );

        return $this->render('admin_event/pending.html.twig', [
            'events' => $events,
        ]);
    }

    public function validate(Event $event, EntityManagerInterface $em): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        $event->setIsValidated(true);
        $em->flush();

        $this->addFlash('success', 'Événement validé ✅');

        return $this->redirectToRoute('app_admin_event_pending');
    }

    public function delete(Event $event, EntityManagerInterface $em): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        $em->remove($event);
        $em->flush();

        $this->addFlash('success', 'Événement supprimé ❌');

        return $this->redirectToRoute('app_admin_event_pending');
    }
}
