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





        $event = $request->query;
        // Parse the message body and check the signature
        $signature = $request->headers->get('stripe-signature');

        if ($endpoint_secret) {
            try {
                $event = \Stripe\Webhook::constructEvent(
                    $request->getcontent(),
                    $signature,
                    $endpoint_secret
                );
            } catch (\Exception $e) {
                return new JsonResponse([['error' => $e->getMessage(), 'status' => 403]]);
            }
        } else {
            $request->query;
        }

        $type = $event['type'];
        $object = $event['data']['object'];
        $manager = $this->getDoctrine()->getManager();
        $today = date("Y-m-d", strtotime('today'));

        // Handle the event
        switch ($type) {
            case 'charge.succeeded':

                $charge_success = $type->object; // contains a StripePaymentIntent
                $session->set('charge_success', $charge_success);
                break;
            case 'checkout.session.completed':
                $checkout_complet = $type->object; // contains a StripePaymentIntent
                $session->set('checkout_complet', $checkout_complet);
                break;
                // ... handle other event types
            case 'customer.subscription.created':
                $customer_sub_created = $type->object; // contains a StripePaymentIntent
                $session->set('customer_sub_created', $customer_sub_created);
                break;
                // ... handle other event types
            case 'customer.subscription.updated':
                $customer_sub_updated = $type->object; // contains a StripePaymentIntent
                $session->set('customer_sub_updated', $customer_sub_updated);
                break;
                // ... handle other event types
            case 'invoice.payment_succeeded':
                $invoice_payment_succeeded = $type->object; // contains a StripePaymentIntent
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
