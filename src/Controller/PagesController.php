<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class PagesController extends AbstractController
{
    #[Route('/pages', name: 'app_pages')]
    public function index(): Response
    {
        return $this->render('pages/index.html.twig', [
            'controller_name' => 'PagesController',
        ]);
    }

    public function mentions(): Response
    {
        return $this->render('pages/mentions.html.twig');
    }

    public function cgu(): Response
    {
        return $this->render('pages/cgu.html.twig');
    }

    public function cgv(): Response
    {
        return $this->render('pages/cgv.html.twig');
    }

    public function privacy(): Response
    {
        return $this->render('pages/privacy.html.twig');
    }

    public function about(): Response
    {
        return $this->render('pages/about.html.twig');
    }

}
