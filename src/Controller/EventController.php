<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class EventController extends AbstractController
{
    #[Route('/evenements', name: 'event_list')]
    #[Route('/events')]

    public function index(): Response
    {
        $events = ["Concert", "Cinéma", "Plage"];
        // function qui permet de retourner une vue twig
        return $this->render('event/index.html.twig', [
            'events' => $events,
        ]);
    }

    #[Route('/evenements/{id}', name: 'event_show', requirements: ['id' => '\d+'])]
    public function show($id): Response
    {
        dump($id);
        return $this->render('event/show.html.twig', [
            'id' => $id, 'name' => 'Symfony'
        ]);
    }
}
