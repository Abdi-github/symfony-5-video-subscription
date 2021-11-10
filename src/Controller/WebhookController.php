<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\VideoRepository;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\RequestStack;

class WebhookController extends AbstractController
{
    private $requestStack;

    public function __construct(RequestStack $requestStack)
    {
        $this->requestStack = $requestStack;
    }

    #[Route('/webhook', name: 'webhook', schemes: ['https'])]
    public function stripeWebhookAction(Request $request, $stripeAPI, User $user)
    {
        \Stripe\Stripe::setApiKey($stripeAPI);
        $webhookSecret = "whsec_Cl1FIM6uvwBE5h9BEpMOBcQsyQl5siZX";
        $payload = @file_get_contents('php://input');
        // $sig_header = $_SERVER['HTTP_STRIPE_SIGNATURE'];
        $header = 'Stripe-Signature';

        $signature = $request->headers->get($header);
        $event = null;

        $session = $this->requestStack->getSession();

        return $this->render('webhook.html.twig');



        try {
            $event = \Stripe\Webhook::constructEvent(
                $payload,
                $webhookSecret,
                $signature
            );

            $session->set('sig', $signature);
            $session->set('hd', 'HEader');


            //, $sig_header
        } catch (\UnexpectedValueException $e) {
            // Invalid payload
            $session->set('hd', 'Invalid payload');

            return new Response(Response::HTTP_BAD_REQUEST);
            exit();
        } catch (\Stripe\Exception\SignatureVerificationException $e) {
            // Invalid signature
            $session->set('hd', 'Invalid signature');
            return new Response(Response::HTTP_BAD_REQUEST);
            exit();
        }

        // Handle the event
        switch ($event->type) {
            case 'invoice.updated':
                $user->setFreePlanUsed(false);
                $em = $this->getDoctrine()->getManager();
                $em->persist($user);
                $em->flush($user);

                break;

                // ... handle other event types
            default:
                // Unexpected event type

                return new Response(Response::HTTP_BAD_REQUEST);
                exit();
        }

        return new Response(Response::HTTP_OK);
    }
}
