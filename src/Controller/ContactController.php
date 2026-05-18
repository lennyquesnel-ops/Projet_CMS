<?php

namespace App\Controller;

use App\Entity\Contact;
use App\Form\ContactType;
use App\Service\TurnstileCaptchaVerifier;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormError;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Address;
use Symfony\Component\Mime\Email;
use Symfony\Component\Routing\Attribute\Route;

class ContactController extends AbstractController
{
    public function __construct(
        private readonly TurnstileCaptchaVerifier $captchaVerifier,
        private readonly MailerInterface $mailer,
        private readonly LoggerInterface $logger,
        private readonly string $contactRecipientEmail,
        private readonly string $mailSenderEmail,
        private readonly string $turnstileSiteKey,
    ) {
    }

    #[Route('/contact', name: 'app_contact', methods: ['GET', 'POST'])]
    public function index(Request $request, EntityManagerInterface $entityManager): Response
    {
        $contact = new Contact();

        $form = $this->createForm(ContactType::class, $contact);
        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            $captchaToken = $request->request->get('cf-turnstile-response');

            if (!$this->captchaVerifier->verify($captchaToken, $request->getClientIp())) {
                $form->addError(new FormError('La vérification anti-spam a échoué. Veuillez réessayer.'));
            }
        }

        if ($form->isSubmitted() && $form->isValid()) {
            $contact->setDate(new \DateTime());

            $entityManager->persist($contact);
            $entityManager->flush();

            try {
                $this->mailer->send($this->createContactEmail($contact));
                $this->addFlash('success', 'Votre message a bien été envoyé.');
            } catch (TransportExceptionInterface $exception) {
                $this->logger->error('Erreur lors de l’envoi du mail de contact.', [
                    'exception' => $exception,
                    'contact_id' => $contact->getId(),
                ]);

                $this->addFlash('danger', 'Votre message a bien été enregistré, mais la notification par email n’a pas pu être envoyée.');
            }

            return $this->redirectToRoute('app_contact');
        }

        return $this->render('contact/index.html.twig', [
            'form' => $form,
            'turnstile_site_key' => $this->turnstileSiteKey,
        ]);
    }

    private function createContactEmail(Contact $contact): Email
    {
        $nomComplet = trim(sprintf('%s %s', $contact->getPrenom(), $contact->getNom()));
        $objet = $contact->getObjet() ?: 'Message de contact';

        return (new Email())
            ->from(new Address($this->mailSenderEmail, 'ATAIS Informatique'))
            ->to($this->contactRecipientEmail)
            ->replyTo(new Address($contact->getEmail(), $nomComplet))
            ->subject('[ATAIS] Nouveau message : ' . $objet)
            ->text($this->createPlainTextEmail($contact))
            ->html($this->renderView('emails/contact_message.html.twig', [
                'contact' => $contact,
            ]));
    }

    private function createPlainTextEmail(Contact $contact): string
    {
        return sprintf(
            "Nouveau message depuis le formulaire de contact ATAIS Informatique\n\nNom : %s\nPrénom : %s\nEmail : %s\nObjet : %s\nDate : %s\n\nMessage :\n%s\n",
            $contact->getNom(),
            $contact->getPrenom(),
            $contact->getEmail(),
            $contact->getObjet(),
            $contact->getDate()?->format('d/m/Y') ?? '',
            $contact->getMessage()
        );
    }
}