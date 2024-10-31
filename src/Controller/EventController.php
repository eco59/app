<?php

namespace App\Controller;

use App\Entity\Event;
use App\Entity\Favori;
use App\Form\AjoutEventFormType;
use App\Repository\EventRepository;
use App\Repository\UsersRepository;
use App\Service\PictureService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;


class EventController extends AbstractController
{

    #[Route('/event', name: 'app_event')]
    public function event(EventRepository $eventRepository): Response
    {
        // Ne récupérer que les événements approuvés
        $event = $eventRepository->findBy(['status' => 'approved']);
        return $this->render('event/event.html.twig', compact('event'));
    }




    #[Route('/event_detail/{id}', name: 'app_event_detail', requirements: ['id' => '\d+'])]
    public function event_detail(int $id, EntityManagerInterface $entityManager, Request $request): Response
    {
        $event = $entityManager->getRepository(Event::class)->find($id);
        $user = $this->getUser();

        if (!$event) {
            throw $this->createNotFoundException('L\'événement n\'existe pas.');
        }

        // Récupérer les favoris non bloqués
        $nombreParticipants = count(array_filter($event->getFavoris()->toArray(), function ($favori) {
            return !$favori->isBlocked(); // Inclure uniquement les favoris non bloqués
        }));

        if ($user) {
            $favori = $entityManager->getRepository(Favori::class)->findOneBy(['idPseudo' => $user, 'idEvent' => $event]);

            if ($favori && $favori->isBlocked()) {
                $this->addFlash('danger', 'Vous avez été bloqué pour cet événement.');
                return $this->redirectToRoute('app_event');
            }
        }

        // Récupération de la date actuelle
        $currentDateTime = new \DateTime();

        // Déterminer si l'événement a déjà commencé
        $hasStarted = $currentDateTime >= $event->getDateHeureDebut();

        // Déterminer si l'événement est terminé
        $isFinished = $currentDateTime >= $event->getDateHeureFin();

        // Déterminer si l'événement est dans les 30 minutes
        $isThirtyMinutesBefore = $event->getDateHeureDebut() <= $currentDateTime->modify('+30 minutes') && $event->getDateHeureDebut() > $currentDateTime;

        // Récupérer l'état d'activation
        $isActive = $request->getSession()->get('is_active_' . $event->getId(), false);

        // Rendu de la page des détails de l'événement
        return $this->render('event/event_detail.html.twig', [
            'event' => $event,
            'hasStarted' => $hasStarted,
            'isThirtyMinutesBefore' => $isThirtyMinutesBefore,
            'isActive' => $isActive,
            'isFinished' => $isFinished,
            'nombreParticipants' => $nombreParticipants,
        ]);
    }

    






    #[Route('/event_started/{id}', name: 'app_event_started', requirements: ['id' => '\d+'])]
    public function event_started(int $id, EntityManagerInterface $entityManager): Response
    {
        // Récupération de l'événement par son ID
        $event = $entityManager->getRepository(Event::class)->find($id);

        if (!$event) {
            throw $this->createNotFoundException('L\'événement n\'existe pas.');
        }

        // Logique pour traiter la participation à l'événement
        // Par exemple, enregistrez que l'utilisateur a rejoint cet événement

        return $this->render('event/event_started.html.twig', [
            'event' => $event,
        ]);
    }







    #[Route('/activate_event/{id}', name: 'app_activate_event', requirements: ['id' => '\d+'])]
public function activate_event(int $id, EntityManagerInterface $entityManager, Request $request): Response
{
    // Récupération de l'événement par son ID
    $event = $entityManager->getRepository(Event::class)->find($id);

    if (!$event) {
        throw $this->createNotFoundException('L\'événement n\'existe pas.');
    }

    // Stocker dans la session que l'utilisateur a activé l'événement
    $this->addFlash('success', 'L\'événement a été activé pour rejoindre !');
    $request->getSession()->set('is_active_' . $event->getId(), true);

    return $this->redirectToRoute('app_event_detail', ['id' => $id]);
}

    





    #[Route('/ajout_event', name: 'app_ajout_event')]
    public function add_event(
        Request $request,
        EntityManagerInterface $em,
        UsersRepository $usersRepository,
        PictureService $pictureService
    ): Response {
        $event = new Event();
        $form = $this->createForm(AjoutEventFormType::class, $event);
    
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            // Assigne l'utilisateur connecté à l'événement
            $event->setPseudo($this->getUser());
    
            // Gère l'image si elle est fournie
            $Image = $form->get('Image')->getData();
            if ($Image) {
                $imageName = $pictureService->square($Image, 'Event', 300);
                $event->setImage($imageName);
            }
    
            // Enregistre l'événement dans la base de données
            $em->persist($event);
            $em->flush();
    
            // Ajoute un message flash pour indiquer le succès de l'opération
            $this->addFlash('success', 'L\'événement a bien été ajouté');
            return $this->redirectToRoute('app_event');
        }
    
        // Rendre la vue avec le formulaire, même en cas d'erreurs
        return $this->render('event/ajout_event.html.twig', [
            'form' => $form->createView(),
        ]);
    }
    







    #[Route('/mes_events', name: 'app_mes_events')]
    public function userEvents(EventRepository $eventRepository): Response
    {
        // Récupération de l'utilisateur connecté
        $user = $this->getUser();

        // Récupération des événements créés par cet utilisateur
        $events = $eventRepository->findBy(['pseudo' => $user]);

        return $this->render('event/mes_events.html.twig', [
            'events' => $events,
        ]);
    }







    
    #[Route('/modifier_event/{id}', name: 'app_modifier_event', requirements: ['id' => '\d+'])]
    public function modifyEvent(int $id, Request $request, EntityManagerInterface $em, PictureService $pictureService): Response
    {
        $event = $em->getRepository(Event::class)->find($id);

        // Vérifier si l'événement appartient à l'utilisateur connecté
        if ($event->getPseudo() !== $this->getUser()) {
            throw $this->createAccessDeniedException('Vous ne pouvez pas modifier cet événement.');
        }

        $form = $this->createForm(AjoutEventFormType::class, $event);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Si l'image a été modifiée
            $Image = $form->get('Image')->getData();
            if ($Image) {
                $imageName = $pictureService->square($Image, 'Event', 300);
                $event->setImage($imageName);
            }

            // Mettre à jour le statut à "pending" après modification
            $event->setStatus('pending');

            $em->flush();

            $this->addFlash('success', 'L\'événement a été modifié et est en attente de révision.');
            return $this->redirectToRoute('app_mes_events');
        }

        return $this->render('event/modifier_event.html.twig', [
            'form' => $form,
            'event' => $event
        ]);
    }
    
}
