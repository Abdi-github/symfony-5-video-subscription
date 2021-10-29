<?php

namespace App\Controller;

use App\Entity\User;
use App\Security\EmailVerifier;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mime\Address;
use Symfony\Component\Routing\Annotation\Route;

class UserProfileController extends AbstractController
{

    private $emailVerifier;

    public function __construct(EmailVerifier $emailVerifier)
    {
        $this->emailVerifier = $emailVerifier;
    }

    #[Route('/user/profile/{user}', name: 'user_profile')]
    public function index(User $user): Response
    {

        // \dd($user->getSubscription()->getPlan());
        return $this->render('user_profile/index.html.twig', [
            'user' => $user
        ]);
    }

    #[Route('/user/profile-update/{user}', name: 'user_profile_update')]
    public function edit(Request $request, User $user): Response
    {
        // \dd($request);

        $submittedToken = $request->request->get('token');
        // \dd($submittedToken);
        $submittedFirstName = \trim($request->request->get('firstname'));
        $submittedLastName = \trim($request->request->get('lastname'));
        $submittedEmail = \trim($request->request->get('email'));
        // \dd($submittedFirstName);


        // \dd($currentUser);

        if ($this->isCsrfTokenValid('change-user-detail', $submittedToken)) {


            $user->setFirstName($submittedFirstName);
            $user->setLastName($submittedLastName);
            $user->setEmail($submittedEmail);


            $em = $this->getDoctrine()->getManager();
            $em->persist($user);
            $em->flush();

            if ($user->isVerified() == false) {
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
        }


        return $this->redirectToRoute('user_profile', ['user' => $user->getId()]);
    }
}
