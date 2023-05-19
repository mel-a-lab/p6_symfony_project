<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\RegistrationFormType;
use Symfony\Component\Mime\Address;
use App\Security\AppCustomAuthenticator;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Security\Http\Authentication\UserAuthenticatorInterface;

class RegistrationController extends AbstractController
{
    #[Route('/register', name: 'app_register')]
    public function register(Request $request, UserPasswordHasherInterface $userPasswordHasher, UserAuthenticatorInterface $userAuthenticator, AppCustomAuthenticator $authenticator, EntityManagerInterface $entityManager, MailerInterface $mailer): Response
    {
        $user = new User();
        $form = $this->createForm(RegistrationFormType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // encode the plain password
            $user->setPassword(
                $userPasswordHasher->hashPassword(
                    $user,
                    $form->get('plainPassword')->getData()
                )
            );

            // Generate an activation token
            $user->setActivationToken(rtrim(strtr(base64_encode(random_bytes(32)), '+/', '-_'), '='));

            // Initially, user is not verified
            $user->setIsVerified(false);

            $entityManager->persist($user);
            $entityManager->flush();

            // Send confirmation email
            $email = (new TemplatedEmail())
                ->from(new Address('contact@snowtricks.com', 'No Reply'))
                ->to($user->getEmail())
                ->subject('Please confirm your email')
                ->htmlTemplate('registration/confirmation_email.html.twig')
                ->context([
                    'activationToken' => $user->getActivationToken(),
                ])
            ;

            $mailer->send($email);

            // Redirect to the login page after successful registration
            return $this->redirectToRoute('app_login');
        }

        return $this->render('registration/register.html.twig', [
            'registrationForm' => $form->createView(),
        ]);
    }

    #[Route('/activate/{token}', name: 'app_activate_account')]
    public function activateAccount(string $token, EntityManagerInterface $entityManager): Response
    {
        $user = $entityManager->getRepository(User::class)->findOneBy(['activationToken' => $token]);

        if (!$user) {
            throw $this->createNotFoundException('No user found for activation token ' . $token);
        }

        // When the user activates their account, set "is_verified" to true
        $user->setIsVerified(true);
        $user->setActivationToken(null);

        $entityManager->flush();

        // @TODO: Add a flash message, redirect to the login page, etc.

        return $this->redirectToRoute('app_login');
    }
}
