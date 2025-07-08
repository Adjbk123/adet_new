<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Symfony\Component\HttpFoundation\Request;

final class AccueilController extends AbstractController
{
    #[Route('/', name: 'app_accueil')]
    public function index(): Response
    {
        return $this->render('accueil/index.html.twig', [
            'controller_name' => 'AccueilController',
        ]);
    }
    #[Route('/admin', name: 'app_admin')]
    public function dashboard(): Response
    {
        return $this->render('accueil/admin.html.twig', [
            'controller_name' => 'AccueilController',
        ]);
    }
    #[Route('/contact', name: 'app_contact')]
    public function contact(Request $request, MailerInterface $mailer): Response
    {
        $success = false;
        if ($request->isMethod('POST')) {
            $nom = $request->request->get('nom');
            $email = $request->request->get('email');
            $message = $request->request->get('message');
            $mail = (new Email())
                ->from($email)
                ->to('no-reply@securevotebenin.site:')
                ->subject('Nouveau message de contact AdET')
                ->text("Nom: $nom\nEmail: $email\n\n$message");
            $mailer->send($mail);
            $success = true;
        }
        return $this->render('accueil/contact.html.twig', [
            'success' => $success
        ]);
    }
    #[Route('/appropos', name: 'app_appropos')]
    public function appropos(): Response
    {
        return $this->render('accueil/appropos.html.twig');
    }
}
