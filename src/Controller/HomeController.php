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
        return $this->render('home.html.twig');
    }

    private function tricksManagement(): void
    {
        $this->postManager = new PostManager();
        $posts = $this->postManager->getPosts();
        $this->view = new View('PostManagement');
        $this->view->generate(array('posts' => $posts));
    }

}