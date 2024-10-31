<?php

namespace App\Controller;

use App\Entity\Event;
use App\Entity\Favori;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class FavoriController extends AbstractController
{
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }




    #[Route('/favori/add/{id}', name: 'add_favori')]
    public function add(Event $event): Response
    {
        $user = $this->getUser();

        if (!$user) {
            throw $this->createAccessDeniedException('Vous devez être connecté pour ajouter un favori.');
        }

        $favoriRepo = $this->entityManager->getRepository(Favori::class);
        $favoriExist = $favoriRepo->findOneBy(['idPseudo' => $user, 'idEvent' => $event]);

        if ($favoriExist) {
            $this->addFlash('warning', 'Cet événement est déjà dans vos favoris.');
            return $this->redirectToRoute('app_event');
        }

        $favori = new Favori();
        $favori->setIdPseudo($user);
        $favori->setIdEvent($event);

        $this->entityManager->persist($favori);
        $this->entityManager->flush();

        $this->addFlash('success', 'Événement ajouté à vos favoris.');
        
        // Rediriger vers la page de détail de l'événement après ajout
        return $this->redirectToRoute('app_mes_favoris');
    }




    #[Route('/favori/remove/{id}', name: 'remove_favori')]
    public function remove(Event $event): Response
    {
        $user = $this->getUser();

        if (!$user) {
            throw $this->createAccessDeniedException('Vous devez être connecté pour retirer un favori.');
        }

        $favoriRepo = $this->entityManager->getRepository(Favori::class);
        $favori = $favoriRepo->findOneBy(['idPseudo' => $user, 'idEvent' => $event]);

        if (!$favori) {
            $this->addFlash('warning', 'Cet événement n\'est pas dans vos favoris.');
            return $this->redirectToRoute('app_event');
        }

        $this->entityManager->remove($favori);
        $this->entityManager->flush();

        $this->addFlash('success', 'Événement retiré de vos favoris.');
        return $this->redirectToRoute('app_mes_favoris');
    }




    #[Route('/favori/mes-favoris', name: 'app_mes_favoris')]
    public function showFavorites(): Response
    {
        $user = $this->getUser();

        if (!$user) {
            throw $this->createAccessDeniedException('Vous devez être connecté pour voir vos favoris.');
        }

        $favoriRepo = $this->entityManager->getRepository(Favori::class);
        $favoris = $favoriRepo->findBy(['idPseudo' => $user]);

        return $this->render('users/favori.html.twig', [
            'favoris' => $favoris,
        ]);
    }




    #[Route('/historique/score', name: 'app_historique_score')]
    public function historiqueScore(): Response
    {
        $user = $this->getUser();

        if (!$user) {
            throw $this->createAccessDeniedException('Vous devez être connecté pour voir votre historique.');
        }

        // Récupérer uniquement les événements créés par l'utilisateur connecté
        $eventRepo = $this->entityManager->getRepository(Event::class);
        $userEvents = $eventRepo->findBy(['pseudo' => $user]);

        // Construction du tableau des événements avec le nombre de favoris
        $eventsWithCounts = [];
        foreach ($userEvents as $event) {
            $eventsWithCounts[] = [
                'event' => $event,
                'count' => count($event->getFavoris())
            ];
        }

        return $this->render('event/historique_score.html.twig', [
            'events' => $eventsWithCounts,
        ]);
    }









    
    #[Route('/favori/block/{favoriId}', name: 'block_favori')]
    public function blockFavori(int $favoriId): Response
    {
        $this->denyAccessUnlessGranted('ROLE_EMPLOYEE');

        $favori = $this->entityManager->getRepository(Favori::class)->find($favoriId);

        if (!$favori) {
            throw $this->createNotFoundException("Favori introuvable.");
        }

        $favori->setBlocked(true);
        $this->entityManager->flush();

        $this->addFlash('success', "L'utilisateur a été bloqué pour cet événement.");

        return $this->redirectToRoute('app_event_approved');
    }

}
