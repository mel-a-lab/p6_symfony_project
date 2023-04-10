<?php

namespace App\Controller;

use App\Form\LoginFormType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;




class UserController extends AbstractController
{
    #[Route('/user', name: 'app_user')]
    public function index(): Response
    {   
        if ($this->getUser()) {
            return $this->redirectToRoute('home');
        }
        return $this->render('user/index.html.twig', [
            'controller_name' => 'UserController',
        ]);
    }
    
    #[Route('/register', name: 'register')]
    public function register(Request $request, UserPasswordEncoderInterface $passwordEncoder): Response
    {
        $user = new User();
        $form = $this->createForm(RegisterFormType::class, $user);
    
        $form->handleRequest($request);
    
        if ($form->isSubmitted() && $form->isValid()) {
            // Récupérer la valeur de plainPassword et hacher le mot de passe
            $plainPassword = $form->get('plainPassword')->getData();
            $encodedPassword = $passwordEncoder->encodePassword($user, $plainPassword);
            $user->setPassword($encodedPassword);
    
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($user);
            $entityManager->flush();
    
            return $this->redirectToRoute('home');
        }
    
        return $this->render('user/register.html.twig', [
            'form' => $form->createView(),
        ]);
    }

     /**
     * @Route("/login", name="app_login", methods={"GET", "POST"})
     */
    public function login(Request $request, AuthenticationUtils $authenticationUtils): Response
{
    // Créez le formulaire de connexion
    $form = $this->createForm(LoginFormType::class);

    // Récupérez le message d'erreur s'il y en a un
    $error = $authenticationUtils->getLastAuthenticationError();

    // Récupérez le dernier nom d'utilisateur entré par l'utilisateur
    $lastUsername = $authenticationUtils->getLastUsername();

    return $this->render('user/signin.html.twig', [
        'form' => $form->createView(),
        'last_username' => $lastUsername,
        'error' => $error
    ]);
}



}

