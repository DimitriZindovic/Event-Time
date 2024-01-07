<?php

namespace App\Controller;

use App\Service\EventService;
use App\Entity\Event;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
// use Symfony\Component\Form\Extension\Core\Type\TextType;
// use Symfony\Component\Form\Extension\Core\Type\MoneyType;
// use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
// use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use App\Form\EventType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Http\Attribute\IsGranted;

class EventController extends AbstractController
{
    #[Route('/evenements', name: 'event_list')]
    #[Route('/events')]

    public function index(Request $request, EventService $eventService, ManagerRegistry $doctrine): Response
    {
        $query = $request->get('search');
        $events = $doctrine->getRepository(Event::class)->search($query);
        // $events = ["Concert", "Cinéma", "Plage"];

        // dump($eventService);
        // function qui permet de retourner une vue twig
        dump($request->get('search'));

        return $this->render('event/index.html.twig', [
            'events' => $events,
        ]);
    }

    #[Route('/evenements/{id}', name: 'event_show', requirements: ['id' => '\d+'])]
    #[IsGranted('ROLE_USER')]

    public function show(EventService $eventService, ManagerRegistry $doctrine, Event $event): Response
    {
        // $event = $eventService->find($id);
        // $event = $doctrine->getRepository(Event::class)->find($id);

        // if (!$event) {
        //     throw $this->createNotFoundException();
        // }



        return $this->render('event/show.html.twig', [
            'event' => $event,
        ]);
    }

    #[Route('/evenements/creer', name: 'event_create')]
    #[IsGranted('ROLE_ADMIN')]

    public function create(Request $request, ManagerRegistry $doctrine)
    {

        $event = new Event();

        // $event->setName("Concert");
        // $event->setPrice(10.99);
        // $event->setStartAt(new \DateTime("2021-06-01 20:00:00"));
        // $event->setEndAt(new \DateTime("2021-06-01 23:00:00"));

        // $form = $this->createFormBuilder($event)
        //     ->add('name', TextType::class)
        //     ->add('price', MoneyType::class)
        //     ->add('startAt', DateTimeType::class)
        //     ->add('endAt', DateTimeType::class)
        //     ->add('add', SubmitType::class, ['label' => 'Ajouter'])
        //     ->getForm();

        $form = $this->createForm(EventType::class, $event, [
            'validation_groups' => ['Default', 'create'],
        ]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            if ($file = $event->getPosterFile()) {
                $filename = md5(uniqid()) . '.' . $file->guessExtension();
                $file->move('./images/events', $filename);
                $event->setPoster($filename);
            }

            $event->setUser($this->getUser());

            $em = $doctrine->getManager();
            $em->persist($event);
            $em->flush();

            $this->addFlash('success', 'L\'événement ' . $event->getName() . ' a bien été créé');

            return $this->redirectToRoute('event_list');
        }

        return $this->render('event/create.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/evenements/{id}/modifier', name: 'event_edit')]
    #[IsGranted('ROLE_ADMIN')]

    public function edit(Request $request, ManagerRegistry $doctrine, Event $event)
    {
        $form = $this->createForm(EventType::class, $event);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            if ($file = $event->getPosterFile()) {
                // Suppression de l'ancienne image
                if ($poster = $event->getPoster()) {
                    @unlink('./images/events/' . $poster);
                }
                $filename = md5(uniqid()) . '.' . $file->guessExtension();
                $file->move('./images/events', $filename);
                $event->setPoster($filename);
            }

            $em = $doctrine->getManager();
            $em->flush();

            $this->addFlash('success', 'L\'événement ' . $event->getName() . ' a bien été modifié');

            return $this->redirectToRoute('event_list');
        }

        return $this->render('event/edit.html.twig', [
            'form' => $form->createView(),
            'event' => $event,
        ]);
    }

    #[Route('/evenements/{id}/supprimer', name: 'event_delete')]
    #[IsGranted('ROLE_ADMIN')]

    public function delete(Request $request, ManagerRegistry $doctrine, Event $event)
    {
        if ($this->isCsrfTokenValid('delete-' . $event->getId(), $request->get('token'))) {
            if ($poster = $event->getPoster()) {
                @unlink('./images/events/' . $poster);
            }
            $em = $doctrine->getManager();
            $em->remove($event);
            $em->flush();

            $this->addFlash('success', 'L\'événement ' . $event->getName() . ' a bien été supprimé');
        }

        return $this->redirectToRoute('event_list');
    }
}
