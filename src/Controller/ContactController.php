<?php

namespace App\Controller;

use App\Entity\Contact;
use App\Form\ContactType;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Routing\Annotation\Route;

class ContactController extends AbstractController
{
    #[Route('/contact', name: 'contact')]
    public function index(Request $request, ManagerRegistry $doctrine, MailerInterface $mailer): Response
    {
        $contact = new Contact;

        $formContact = $this->createForm(ContactType::class, $contact);

        $formContact->handleRequest($request);
        if($formContact->isSubmitted() && $formContact->isValid())
        {
            $entityManager = $doctrine->getManager();

            $entityManager->persist($contact);
            $entityManager->flush();

            #étape: Envoie de l'email
            $email = (new TemplatedEmail())
            ->from($contact->getEmail())
            ->to('contact@monsite.com')
            ->subject('Demande de contact')

            // path of the Twig template to render
            ->htmlTemplate('emails/contact.html.twig')

            // pass variables (name => value) to the template
            ->context([
                   "contact" => $contact
            ]);

              $mailer->send($email);

            $this->addFlash('contact_success', "votre mail a bien été enoyé, On revient vers vous dés que possible😀");
            

            #on créé une maessage flash
            $this->addFlash('contact_success', "votre mail a bien été enoyé, On revient vers vous dés que possible😀");

        }
        return $this->render('contact/index.html.twig', [

            "formContact" => $formContact->createView()
        ]);
    }

    }

