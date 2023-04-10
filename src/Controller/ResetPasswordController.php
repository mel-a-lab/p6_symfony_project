<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\ResetPasswordType;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class ResetPasswordController extends AbstractController
{
    #[Route('/reset-password', name: 'reset_password_request')]
    public function request(Request $request, \Swift_Mailer $mailer, UserRepository $userRepository): Response
    {
        if ($request->isMethod('POST')) {
            $email = $request->request->get('email');
            $user = $userRepository->findOneBy(['email' => $email]);

            if ($user === null) {
                $this->addFlash('danger', 'Email Inconnu');
                return $this->redirectToRoute('reset_password_request');
            }
            $token = rtrim(strtr(base64_encode(random_bytes(32)), '+/', '-_'), '=');
            $user->setResetToken($token);
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->flush();
            $url = $this->generateUrl('reset_password', ['token' => $token], true);

            $message = (new \Swift_Message('Forgot Password'))
                ->setFrom('noreply@snowtricks.com')
                ->setTo($user->getEmail())
                ->setBody(
                    $this->renderView(
                        'emails/resetPassword.html.twig',
                        ['url' => $url]
                    ),
                    'text/html'
                );
            $mailer->send($message);

            $this->addFlash('success', 'Un email vous a été envoyé');

            return $this->redirectToRoute('home');
        }
        return $this->render('reset_password/request.html.twig');
    }

    #[Route('/reset-password/{token}', name: 'reset_password')]
    public function reset(Request $request, string $token, UserPasswordEncoderInterface $passwordEncoder, EntityManagerInterface $entityManager): Response
    {
        $user = $entityManager->getRepository(User::class)->findOneBy(['resetToken' => $token]);

        if ($user === null) {
            $this->addFlash('danger', 'Token Inconnu');
            return $this->redirectToRoute('reset_password_request');
        }

        if ($request->isMethod('POST')) {
            $user->setResetToken(null);
            $user->setPassword(
                $passwordEncoder->encodePassword(
                    $user,
                    $request->request->get('password')
                )
            );
            $entityManager->persist($user);
            $entityManager->flush();
            $this->addFlash('success', 'Mot de passe mis à jour');

            return $this->redirectToRoute('home');
        }

        return $this->render('reset_password/reset.html.twig', ['token' => $token]);
    }
}
