<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\RegistrationFormType;
use Symfony\Component\Mime\Address;
//use App\Security\EmailVerifier;
use App\Security\AppCustomAuthenticator;
//use Symfony\Bridge\Twig\Mime\TemplatedEmail;
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

//use SymfonyCasts\Bundle\VerifyEmail\Exception\VerifyEmailExceptionInterface;

class RegistrationController extends AbstractController
{
    /*  private EmailVerifier $emailVerifier;
    public function __construct(EmailVerifier $emailVerifier)
    {
    $this->emailVerifier = $emailVerifier;
    }
    */
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

            $entityManager->persist($user);
            $entityManager->flush();



            $mail = (new TemplatedEmail())
                ->from(new Address($user->getEmail(), $user->getUsername()))
                ->to('melanie.dussenne@gmail.com')
                ->subject('Création Compte')
                ->htmlTemplate('mail/template.html.twig')
                ->context([
                    'firstname' => 'Joe'
                ])
            ;

            $mailer->send($mail);




            return $userAuthenticator->authenticateUser(
                $user,
                $authenticator,
                $request
            );
        }

        return $this->render('registration/register.html.twig', [
            'registrationForm' => $form->createView(),
        ]);
    }

/* #[Route('/verify/email', name: 'app_verify_email')]
public function verifyUserEmail(Request $request, TranslatorInterface $translator): Response
{
$this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
// validate email confirmation link, sets User::isVerified=true and persists
try {
$this->emailVerifier->handleEmailConfirmation($request, $this->getUser());
} catch (VerifyEmailExceptionInterface $exception) {
$this->addFlash('verify_email_error', $translator->trans($exception->getReason(), [], 'VerifyEmailBundle'));
return $this->redirectToRoute('app_register');
}
// @TODO Change the redirect on success and handle or remove the flash message in your templates
$this->addFlash('success', 'Your email address has been verified.');
return $this->redirectToRoute('app_register');
} */
}