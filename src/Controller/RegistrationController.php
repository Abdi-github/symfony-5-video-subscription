<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\RegistrationFormType;
use App\Repository\UserRepository;
use App\Security\EmailVerifier;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mime\Address;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use SymfonyCasts\Bundle\VerifyEmail\Exception\VerifyEmailExceptionInterface;

class RegistrationController extends AbstractController
{
    private $emailVerifier;

    public function __construct(EmailVerifier $emailVerifier)
    {
        $this->emailVerifier = $emailVerifier;
    }

    #[Route('/confirm', name: 'app_confirm')]
    public function confirmPageRedirect(): Response
    {
        return $this->render('common/confirm_redirect.html.twig');
    }

    // #[Route('/register', name: 'app_register')]
    // public function register(Request $request, UserPasswordHasherInterface $userPasswordHasherInterface): Response
    // {
    //     dd($request);
    //     $user = new User();
    //     $form = $this->createForm(RegistrationFormType::class, $user);
    //     $form->handleRequest($request);

    //     if ($form->isSubmitted() && $form->isValid()) {
    //         // encode the plain password
    //         $user->setPassword(
    //             $userPasswordHasherInterface->hashPassword(
    //                 $user,
    //                 $form->get('password')->getData()
    //             )
    //         );

    //         $user->setFirstName($form->get('first_name')->getData());
    //         $user->setLastName($form->get('last_name')->getData());

    //         if ($request->attributes->get('_route') == 'app_register_admin') {
    //             $user->setRoles(['ROLE_ADMIN']);
    //         }

    //         $user->setRoles(['ROLE_USER']);
    //         $user->setFreePlanUsed(false);
    //         $user->setPaymentStatus(false);


    //         $entityManager = $this->getDoctrine()->getManager();
    //         $entityManager->persist($user);
    //         $entityManager->flush();

    //         // generate a signed url and email it to the user
    //         $this->emailVerifier->sendEmailConfirmation(
    //             'app_verify_email',
    //             $user,
    //             (new TemplatedEmail())
    //                 ->from(new Address('swift-app@gmx.ch', 'Swift'))
    //                 ->to($user->getEmail())
    //                 ->subject('Please Confirm your Email')
    //                 ->htmlTemplate('registration/confirmation_email.html.twig')
    //         );
    //         // do anything else you need here, like send an email

    //         return $this->redirectToRoute('app_confirm');
    //     }

    //     return $this->render('registration/register.html.twig', [
    //         'registrationForm' => $form->createView(),
    //     ]);
    // }
    #[Route('/register', name: 'app_register')]
    public function register(Request $request, UserPasswordHasherInterface $userPasswordHasherInterface): Response
    {
        $user = new User();
        $form = $this->createForm(RegistrationFormType::class, $user);
        $form->handleRequest($request);


        if ($form->isSubmitted() && $form->isValid()) {
            return $this->reg(
                $request,
                $userPasswordHasherInterface
            );
        }

        return $this->render('registration/register.html.twig', [
            'registrationForm' => $form->createView(),
        ]);
    }

    #[Route('/admin/register', name: 'app_register_admin')]
    public function registerAdmin(Request $request, UserPasswordHasherInterface $userPasswordHasherInterface): Response
    {
        $user = new User();
        $form = $this->createForm(RegistrationFormType::class, $user);
        $form->handleRequest($request);


        if ($form->isSubmitted() && $form->isValid()) {
            return $this->reg(
                $request,
                $userPasswordHasherInterface
            );
        }

        return $this->render('registration/register.html.twig', [
            'registrationForm' => $form->createView(),
        ]);




        // if ($form->isSubmitted() && $form->isValid()) {
        //     // encode the plain password
        //     $user->setPassword(
        //         $userPasswordHasherInterface->hashPassword(
        //             $user,
        //             $form->get('password')->getData()
        //         )
        //     );

        //     $user->setFirstName($form->get('first_name')->getData());
        //     $user->setLastName($form->get('last_name')->getData());
        //     $user->setRoles(['ROLE_ADMIN']);
        //     $user->setFreePlanUsed(false);
        //     $user->setPaymentStatus(false);


        //     $entityManager = $this->getDoctrine()->getManager();
        //     $entityManager->persist($user);
        //     $entityManager->flush();

        //     // generate a signed url and email it to the user
        //     $this->emailVerifier->sendEmailConfirmation(
        //         'app_verify_email',
        //         $user,
        //         (new TemplatedEmail())
        //             ->from(new Address('swift-app@gmx.ch', 'Swift'))
        //             ->to($user->getEmail())
        //             ->subject('Please Confirm your Email')
        //             ->htmlTemplate('registration/confirmation_email.html.twig')
        //     );
        //     // do anything else you need here, like send an email

        //     return $this->redirectToRoute('app_confirm');
        // }


    }

    private function reg(Request $request, UserPasswordHasherInterface $userPasswordHasherInterface)
    {
        $user = new User();
        $form = $this->createForm(RegistrationFormType::class, $user);
        $form->handleRequest($request);
        // encode the plain password
        $user->setPassword(
            $userPasswordHasherInterface->hashPassword(
                $user,
                $form->get('password')->getData()
            )
        );

        $user->setFirstName($form->get('first_name')->getData());
        $user->setLastName($form->get('last_name')->getData());

        if ($request->attributes->get('_route') == 'app_register_admin') {
            $user->setRoles(['ROLE_ADMIN']);
        } else if ($request->attributes->get('_route') == 'app_register') {

            $user->setRoles(['ROLE_USER']);
        }

        $user->setFreePlanUsed(false);
        $user->setPaymentStatus(false);


        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->persist($user);
        $entityManager->flush();

        // generate a signed url and email it to the user
        $this->emailVerifier->sendEmailConfirmation(
            'app_verify_email',
            $user,
            (new TemplatedEmail())
                ->from(new Address('swift-app@gmx.ch', 'Swift'))
                ->to($user->getEmail())
                ->subject('Please Confirm your Email')
                ->htmlTemplate('registration/confirmation_email.html.twig')
        );
        // do anything else you need here, like send an email

        return $this->redirectToRoute('app_confirm');
    }



    #[Route('/verify/email', name: 'app_verify_email')]
    public function verifyUserEmail(Request $request, UserRepository $userRepository): Response
    {
        $id = $request->get('id');

        if (null === $id) {
            return $this->redirectToRoute('app_register');
        }

        $user = $userRepository->find($id);

        if (null === $user) {
            return $this->redirectToRoute('app_register');
        }

        // validate email confirmation link, sets User::isVerified=true and persists
        try {
            $this->emailVerifier->handleEmailConfirmation($request, $user);
        } catch (VerifyEmailExceptionInterface $exception) {
            $this->addFlash('verify_email_error', $exception->getReason());

            return $this->redirectToRoute('app_register');
        }

        // @TODO Change the redirect on success and handle or remove the flash message in your templates
        $this->addFlash('success', 'Your email address has been verified.');

        return $this->redirectToRoute('app_login');
    }
}
