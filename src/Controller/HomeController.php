<?php

namespace App\Controller;

use App\Repository\EventRepository;
use DateTimeImmutable;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class HomeController extends AbstractController
{
    public function index(Request $request, EventRepository $er): Response
    {
        $q = trim((string) $request->query->get('q', ''));
        $location = trim((string) $request->query->get('location', ''));
        $period = (string) $request->query->get('period', ''); // week|month|year

        $now = new DateTimeImmutable();
        $qb = $er->createQueryBuilder('e')
            ->andWhere('e.isValidated = :ok')
            ->andWhere('e.eventDate >= :now')
            ->setParameter('ok', true)
            ->setParameter('now', $now);

        if ($q !== '') {
            $qb->andWhere('LOWER(e.title) LIKE :q OR LOWER(e.description) LIKE :q')
               ->setParameter('q', '%' . mb_strtolower($q) . '%');
        }

        if ($location !== '') {
            $loc = '%' . mb_strtolower($location) . '%';

            $qb->andWhere(
                'LOWER(e.city) LIKE :loc OR LOWER(e.region) LIKE :loc OR LOWER(e.country) LIKE :loc OR e.zipcode LIKE :locZip'
            )
            ->setParameter('loc', $loc)
            ->setParameter('locZip', '%' . $location . '%');
        }

        if (in_array($period, ['week', 'month', 'year'], true)) {
            $end = match ($period) {
                'week' => $now->modify('+7 days'),
                'month' => $now->modify('+1 month'),
                'year' => $now->modify('+1 year'),
            };

            $qb->andWhere('e.eventDate <= :end')
               ->setParameter('end', $end);
        }

        $events = $qb->orderBy('e.eventDate', 'ASC')
            ->getQuery()
            ->getResult();

        return $this->render('home/index.html.twig', [
            'events' => $events,
            'filters' => [
                'q' => $q,
                'location' => $location,
                'period' => $period,
            ],
        ]);
    }
}
