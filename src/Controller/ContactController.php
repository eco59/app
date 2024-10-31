<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Form\ContactType;

class ContactController extends AbstractController
{
    #[Route('/contact', name: 'app_contact')]
    public function contact(Request $request, MailerInterface $mailer): Response
    {
        // Création du formulaire
        $form = $this->createForm(ContactType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();
            
            // Créer un email
            $email = (new Email())
                ->from($data['email'])
                ->to('esportify@test.fr')  // Mettez l'email du destinataire ici
                ->subject('Nouvelle demande de contact')
                ->text($data['message']);

            // Envoyer l'email
            $mailer->send($email);

            // Message de confirmation
            $this->addFlash('success', 'Votre message a été envoyé !');

            return $this->redirectToRoute('app_contact');
        }

        // Afficher le formulaire
        return $this->render('contact/contact.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
