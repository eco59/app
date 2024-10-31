<?php

namespace App\Controller;

use App\Entity\Event;
use App\Entity\Favori;
use App\Service\EventManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\Mailer\MailerInterface;
use App\Repository\EventRepository;

#[Route('/event_pending')]
class EventPendingController extends AbstractController
{
    private $eventManager;
    private $em;
    private $mailer;

    public function __construct(EventManager $eventManager, EntityManagerInterface $em, MailerInterface $mailer)
    {
        $this->eventManager = $eventManager;
        $this->em = $em;
        $this->mailer = $mailer;
    }



    
    #[Route('/approve/{id}', name: 'event_pending_approve')]
    #[IsGranted('ROLE_EMPLOYEE')] // Seuls les employés et admins peuvent approuver
    public function approveEvent(int $id): Response
    {
        $event = $this->em->getRepository(Event::class)->find($id);

        if (!$event) {
            throw $this->createNotFoundException('Event not found.');
        }

        // Approbation de l'événement via le service EventManager
        $this->eventManager->approveEvent($event, $this->mailer);

        // Redirection après succès
        return $this->redirectToRoute('event_pending_list', ['success' => 'Event approved successfully.']);
    }





    #[Route('/reject/{id}', name: 'event_pending_reject')]
    #[IsGranted('ROLE_EMPLOYEE')] // Seuls les employés et admins peuvent rejeter
    public function rejectEvent(int $id): Response
    {
        $event = $this->em->getRepository(Event::class)->find($id);

        if (!$event) {
            throw $this->createNotFoundException('Event not found.');
        }

        // Rejet de l'événement via le service EventManager
        $this->eventManager->rejectEvent($event, $this->mailer);

        // Redirection après succès
        return $this->redirectToRoute('event_pending_list', ['success' => 'Event rejected successfully.']);
    }




    #[Route('/list', name: 'event_pending_list')]
    #[IsGranted('ROLE_EMPLOYEE')] // Seuls les employés et admins peuvent voir cette liste
    public function listPendingEvents(): Response
    {
        // Liste des événements en attente (non approuvés)
        $pendingEvents = $this->em->getRepository(Event::class)->findBy(['status' => 'pending']);

        return $this->render('event_pending/list.html.twig', [
            'events' => $pendingEvents,
        ]);
    }


    #[Route('/event_approved', name: 'app_event_approved')]
    #[IsGranted('ROLE_EMPLOYEE')]
    public function event_approved(EventRepository $eventRepository): Response
    {
        // Récupérer uniquement les événements approuvés et actifs (date de fin non dépassée)
        $events = $eventRepository->findApprovedAndActiveEvents();

        // Récupérer les favoris pour chaque événement approuvé
        $eventsWithFavorites = [];
        foreach ($events as $event) {
            $favoris = $this->em->getRepository(Favori::class)->findBy(['idEvent' => $event]);
            $eventsWithFavorites[] = [
                'event' => $event,
                'favoris' => $favoris
            ];
        }

        return $this->render('event/event_approved.html.twig', [
            'eventsWithFavorites' => $eventsWithFavorites,
        ]);
    }
}
