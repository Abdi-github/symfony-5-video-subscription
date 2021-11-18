<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\VideoRepository;
use Psr\Log\LoggerInterface;
use Stripe\Stripe;
use Symfony\Component\HttpFoundation\RequestStack;

class WebhookController extends AbstractController
{
    private $requestStack;

    public function __construct(RequestStack $requestStack)
    {
        $this->requestStack = $requestStack;
    }

    #[Route('/webhook', name: 'webhook')]
    public function stripeWebhookAction(Request $request, $stripeAPI, User $user)
    {
        $session = $this->requestStack->getSession();

        $endpoint_secret = 'whsec_Cl1FIM6uvwBE5h9BEpMOBcQsyQl5siZX';

        $payload = @file_get_contents('php://input');

        $header = 'Stripe-Signature';
        $signature = $request->headers->get($header);

        $sig_header = $signature;
        $event = null;

        try {
            $event = \Stripe\Webhook::constructEvent(
                $payload,
                $sig_header,
                $endpoint_secret
            );
            echo $event->data;
        } catch (\UnexpectedValueException $e) {
            // Invalid payload
            echo $e->getMessage();
            http_response_code(400);
            exit();
        } catch (\Stripe\Exception\SignatureVerificationException $e) {
            // Invalid signature
            echo $e->getMessage();
            http_response_code(400);
            exit();
        }

        // Handle the event
        switch ($event->type) {
            case 'charge.succeeded':

                $charge_success = $event->data->object; // contains a StripePaymentIntent
                $session->set('charge_success', $charge_success);
                break;
            case 'checkout.session.completed':
                $checkout_complet = $event->data->object; // contains a StripePaymentIntent
                $session->set('checkout_complet', $checkout_complet);
                break;
                // ... handle other event types
            case 'customer.subscription.created':
                $customer_sub_created = $event->data->object; // contains a StripePaymentIntent
                $session->set('customer_sub_created', $customer_sub_created);
                break;
                // ... handle other event types
            case 'customer.subscription.updated':
                $customer_sub_updated = $event->data->object; // contains a StripePaymentIntent
                $session->set('customer_sub_updated', $customer_sub_updated);
                break;
                // ... handle other event types
            case 'invoice.payment_succeeded':
                $invoice_payment_succeeded = $event->data->object; // contains a StripePaymentIntent
                $session->set('invoice_payment_succeeded', $invoice_payment_succeeded);
                break;
                // ... handle other event types
            default:
                // Unexpected event type

                return new Response(Response::HTTP_BAD_REQUEST);
                exit();
        }

        return new Response(Response::HTTP_OK);

        // $session = $this->requestStack->getSession();

        // $data = json_decode($request->getContent(), true);



        // $endpoint_secret = 'whsec_Cl1FIM6uvwBE5h9BEpMOBcQsyQl5siZX';

        // $data = json_decode($request->getContent(), true);
        // $session->set('data', $data);




        // $session->set('Req', $request);
    }
}
