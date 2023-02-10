<?php

// src/Controller/HelloController.php
namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController
{
    #[Route('/', 'home.index', methods: ['GET'])]
    public function index() : Response
    {
        $tricks = ['figure1','figure2','figure3','figure4','figure5'];
        return $this->render('home.html.twig',[
            'tricks'=>$tricks
        ]);
    }

    #[Route('/signin', 'signin', methods: ['GET'])]
    public function signin() : Response
    {
        return $this->render('signin.html.twig');
    }

    #[Route('/signup', 'signup', methods: ['GET'])]
    public function signup() : Response
    {
        return $this->render('signup.html.twig');
    }

}