<?php

namespace App\Controller;

use App\Entity\Users;
use App\Entity\Event;
use App\Service\EventManager;
use App\Repository\UsersRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Form\ChangePasswordFormType;
use App\Form\CreateEmployeeFormType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;


class AdminController extends AbstractController
{

    private $eventManager;

    public function __construct(EventManager $eventManager)
    {
        $this->eventManager = $eventManager;
    }


    #[Route(path: '/admin', name: 'app_espace_admin')]
public function admin_espace(EntityManagerInterface $em, Request $request): Response
{
    // Utilisation d'une requête SQL native pour récupérer le nombre d'événements par jour
    $connection = $em->getConnection();
    
    // Vérifiez si une date a été sélectionnée
    $selectedDate = $request->query->get('date', date('Y-m-d')); // Récupérer la date sélectionnée ou utiliser la date actuelle

    // Requête pour obtenir le nombre d'événements pour la date sélectionnée
    $sql = '
        SELECT COUNT(*) AS eventCount
        FROM event
        WHERE DATE(date_heure_debut) = :date
    ';
    
    $stmt = $connection->executeQuery($sql, ['date' => $selectedDate]);

    // Récupérer le nombre total d'événements
    $result = $stmt->fetchAssociative();
    $eventCount = $result['eventCount'] ?? 0; // Utiliser 0 si aucune valeur n'est trouvée

    // Récupérer le nombre d'événements par jour pour le graphique
    $sqlEventsByDay = '
        SELECT DATE(date_heure_debut) AS dateEvent, COUNT(id) AS eventCount
        FROM event
        GROUP BY dateEvent
        ORDER BY dateEvent ASC
    ';

    // Exécuter la requête et récupérer les résultats
    $stmtEventsByDay = $connection->executeQuery($sqlEventsByDay);
    $events = $stmtEventsByDay->fetchAllAssociative();

    // Préparer les données pour Chart.js
    $dates = [];
    $counts = [];

    foreach ($events as $event) {
        $dates[] = $event['dateEvent'];
        $counts[] = (int) $event['eventCount'];
    }

    return $this->render('admin/espace_admin.html.twig', [
        'selectedDate' => $selectedDate,
        'eventCount' => $eventCount,
        'dates' => json_encode($dates),
        'counts' => json_encode($counts),
    ]);
}


    #[Route(path: '/liste_user', name: 'app_liste_user')]
    public function liste_user(UsersRepository $usersRepository): Response
    {
        // Récupérer les utilisateurs avec les rôles ROLE_USER ou ROLE_EMPLOYEE
        $liste_users = $usersRepository->findAllUsersWithRoleUserOrRoleEmployee();

        // Passer la liste d'utilisateurs au template
        return $this->render('admin/liste_user.html.twig', [
            'users' => $liste_users,
        ]);
    }

    #[Route('/admin/user/{id}/change-password', name: 'admin_user_change_password')]
    public function changePassword(int $id, Request $request, UserPasswordHasherInterface $passwordHasher, UsersRepository $usersRepository, EntityManagerInterface $entityManager): Response
    {
        // Récupérer l'utilisateur à partir du UsersRepository grâce à l'ID
        $user = $usersRepository->find($id);

        if (!$user) {
            throw $this->createNotFoundException('Utilisateur non trouvé.');
        }

        // Créer le formulaire pour le changement de mot de passe
        $form = $this->createForm(ChangePasswordFormType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Récupérer le nouveau mot de passe depuis le formulaire
            $newPassword = $form->get('newPassword')->getData();

            // Hash le nouveau mot de passe
            $hashedPassword = $passwordHasher->hashPassword($user, $newPassword);
            $user->setPassword($hashedPassword);

            // Sauvegarder les modifications dans la base de données
            $entityManager->persist($user);
            $entityManager->flush();

            $this->addFlash('success', 'Mot de passe modifié avec succès.');

            return $this->redirectToRoute('app_liste_user'); // Rediriger vers la liste des utilisateurs
        }

        return $this->render('admin/change_password.html.twig', [
            'form' => $form->createView(),
            'user' => $user,
        ]);
    }

    #[Route('/admin/user/{id}/delete', name: 'admin_user_delete', methods: ['POST'])]
    public function deleteUser(int $id, UsersRepository $usersRepository, EntityManagerInterface $entityManager, Request $request): Response
    {
        // Récupérer l'utilisateur via l'ID
        $user = $usersRepository->find($id);

        if (!$user) {
            throw $this->createNotFoundException('Utilisateur non trouvé.');
        }

        // Vérification du token CSRF pour sécuriser l'opération
        if ($this->isCsrfTokenValid('delete' . $user->getId(), $request->request->get('_token'))) {
            // Supprimer l'utilisateur
            $entityManager->remove($user);
            $entityManager->flush();

            $this->addFlash('success', 'Utilisateur supprimé avec succès.');
        } else {
            $this->addFlash('error', 'Échec de la validation du token CSRF.');
        }

        return $this->redirectToRoute('app_liste_user');
    }

    #[Route(path: '/admin/create-employee', name: 'app_create_employee')]
    public function createEmployee(Request $request, UserPasswordHasherInterface $passwordHasher, EntityManagerInterface $entityManager, MailerInterface $mailer): Response
    {
        $user = new Users(); // Créer une nouvelle instance de l'utilisateur

        $form = $this->createForm(CreateEmployeeFormType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Récupérer les données du formulaire
            $user->setRoles(['ROLE_EMPLOYEE']);

            // Hashage du mot de passe
            $hashedPassword = $passwordHasher->hashPassword($user, $user->getPassword());
            $user->setPassword($hashedPassword);

            // Enregistrer l'utilisateur en base de données
            $entityManager->persist($user);
            $entityManager->flush();

            // Envoi de l'email à l'employé
            $email = (new Email())
                ->from('admin@example.com')  // Adresse de l'expéditeur
                ->to($user->getEmail())       // Adresse email de l'employé
                ->subject('Votre compte a été créé')
                ->text('Votre compte a bien été créé, veuillez voir votre administrateur afin de récupérer vos identifiants.');

            $mailer->send($email);

            // Message de succès pour l'admin
            $this->addFlash('success', 'Employé créé avec succès et email envoyé.');

            // Redirection après création
            return $this->redirectToRoute('app_liste_user');
        }

        return $this->render('admin/creation_employee.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/admin/block_user/{id}', name: 'admin_block_user')]
    public function blockUserFromEvent(Event $event): Response
    {
        $this->eventManager->blockUserFromEvent($event);
        $this->addFlash('warning', 'L\'utilisateur a été bloqué de l\'événement et un email lui a été envoyé.');
        
        return $this->redirectToRoute('app_event');
    }

}
