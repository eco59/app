<?php

namespace App\Service;

use App\Entity\Event;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;

class EventManager
{
    private $em;
    private $mailer;

    public function __construct(EntityManagerInterface $em, MailerInterface $mailer)
    {
        $this->em = $em;
        $this->mailer = $mailer;
    }

    public function approveEvent(Event $event): void
    {
        $event->setStatus('approved');
        $this->em->flush();

        $email = (new Email())
            ->from('no-reply@example.com')
            ->to($event->getPseudo()->getEmail())
            ->subject('Your event has been approved')
            ->text('Congratulations, your event has been approved!');
        $this->mailer->send($email);
    }

    public function rejectEvent(Event $event): void
    {
        $event->setStatus('rejected');
        $this->em->flush();

        $email = (new Email())
            ->from('no-reply@example.com')
            ->to($event->getPseudo()->getEmail())
            ->subject('Your event has been rejected')
            ->text('Sorry, your event has been rejected.');
        $this->mailer->send($email);
    }

    public function blockUserFromEvent(Event $event): void
    {
        // Changement du statut à "blocked" pour l'utilisateur
        $event->setStatus('blocked');
        $this->em->flush();

        // Envoi d'un email pour informer l'utilisateur du blocage
        $email = (new Email())
            ->from('no-reply@example.com')
            ->to($event->getPseudo()->getEmail())
            ->subject('You have been blocked from the event')
            ->text('You have been blocked from participating in the event.');
        $this->mailer->send($email);
    }
}
