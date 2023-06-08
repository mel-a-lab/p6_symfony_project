<?php

namespace App\Controller;

use App\Entity\User;
use App\Repository\UserRepository;
use Symfony\Component\Mime\Address;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Security\Csrf\TokenGenerator\TokenGeneratorInterface;

class ForgotPasswordController extends AbstractController
{
    #[Route('/forgot-password', name: 'app_forgot_password_request', methods: ['GET', 'POST'])]
    public function request(Request $request, EntityManagerInterface $entityManager, UserRepository $userRepository, MailerInterface $mailer, TokenGeneratorInterface $tokenGenerator): Response
    {
        if ($request->isMethod('POST')) {
            $username = $request->request->get('username');
            $user = $userRepository->findOneBy(['username' => $username]);

            if ($user) {
                $token = $tokenGenerator->generateToken();
                $user->setResetToken($token);
                $entityManager->flush();

                $mail = (new TemplatedEmail())
                    ->from(new Address('expediteur@demo.test', 'Réinitialisation mot de passe'))
                    ->to($user->getEmail())
                    ->subject('Your password reset request')
                    ->htmlTemplate('security/reset_password_email.html.twig')
                    ->context([
                        'token' => $token,
                        'username' => $user->getUsername(),
                    ])
                ;

                $mailer->send($mail);
                
                $this->addFlash('email_reset', 'Le mail de réinitialisation de mot de passe a bien été envoyé');

                return $this->redirectToRoute('app_login');
            }

            $this->addFlash('error', 'Aucun compte n\'a été trouvé avec ce nom d\'utilisateur
            .');

            return $this->redirectToRoute('app_forgot_password_request');
        }

        return $this->render('security/forgot_password_request.html.twig', [
            'success' => $request->getSession()->getFlashBag()->has('success'),
            'error' => $request->getSession()->getFlashBag()->get('error')
        ]);
    }

    #[Route(path: '/reset-password/{token}', name: 'app_reset_password')]
    public function reset(Request $request, string $token, UserPasswordHasherInterface $passwordEncoder, UserRepository $userRepository, EntityManagerInterface $entityManager): Response
    {
        if ($request->isMethod('POST')) {
            $username = $request->request->get('username');
            $user = $userRepository->findOneBy(['username' => $username, 'resetToken' => $token]);    

            if ($user) {
                $user->setPassword(
                    $passwordEncoder->hashPassword(
                        $user,
                        $request->request->get('password')
                    )
                );
                $user->setResetToken(null);
                $entityManager->flush();

                $this->addFlash('success', 'Votre mot de passe a été modifié.');

                return $this->redirectToRoute('app_login');
            }

            $this->addFlash('error', 'Invalid or expired reset token.');

            return $this->redirectToRoute('app_forgot_password_request');
        }

        return $this->render('security/reset_password.html.twig', [
            'token' => $token,
        ]);
    }
}
