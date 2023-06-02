<?php

// src/Controller/HelloController.php
namespace App\Controller;

use Symfony\Component\Mime\Email;
use App\Repository\TrickRepository;
use Symfony\Component\Mime\Address;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class HomeController extends AbstractController
{
    #[Route('/', 'home.index', methods: ['GET'])]
    public function index(TrickRepository $trickRepository): Response
    {
        $tricks = $trickRepository->findAll();
        return $this->render('home.html.twig', [
            'tricks' => $tricks
        ]);
    }

    #[Route('/signin', 'signin', methods: ['GET'])]
    public function signin(): Response
    {
        return $this->render('signin.html.twig');
    }

    #[Route('/signup', 'signup', methods: ['GET'])]
    public function signup(): Response
    {
        return $this->render('signup.html.twig');
    }


    #[Route('/email')]
    public function sendMail(MailerInterface $mailer, Request $request): void
    {
        // ...

        $mail = (new TemplatedEmail())
            ->from(new Address('expediteur@demo.test', 'Mon nom'))
            ->to('melanie.dussenne@gmail.com')
            ->subject('Mon beau sujet')
            ->htmlTemplate('mail/template.html.twig')
            ->context([
                'firstname' => 'Joe'
            ])
        ;

        $mailer->send($mail);


    }
}