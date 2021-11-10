<?php

namespace App\Controller;

use App\Repository\SubscriptionRepository;
use App\Repository\UserRepository;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Stripe\Stripe;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class SubscriptionController extends AbstractController
{

    private $requestStack;

    public function __construct(RequestStack $requestStack)
    {
        $this->requestStack = $requestStack;
    }




    public function getPricingDetail(SubscriptionRepository $sr)
    {
        $subscriptions = $sr->findAll();
        // \dd($subscriptions);
        return $this->render('common/pricing.html.twig', [
            'subscriptions' => $subscriptions,
        ]);
    }


    #[Route('/subscribe/{user}/{plan}', name: 'checkout')]
    public function subscribe(Request $request,  $stripeAPI, UserRepository $ur)
    {

        // \dd($request->request->get('priceId'));
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_REMEMBERED');

        Stripe::setApiKey($stripeAPI);

        $session = $this->requestStack->getSession();





        $priceId = $request->request->get('priceId');
        if ($priceId) {


            $stripeSession = \Stripe\Checkout\Session::create([
                'success_url' => $this->generateUrl('success_url', [], UrlGeneratorInterface::ABSOLUTE_URL),
                'cancel_url' => $this->generateUrl('cancel_url', [], UrlGeneratorInterface::ABSOLUTE_URL),
                'payment_method_types' => ['card'],
                'mode' => 'subscription',
                'client_reference_id' => $this->getUser()->getId(),
                'customer_email' => $this->getUser()->getEmail(),
                'line_items' => [[
                    'price' => $priceId,
                    // For metered billing, do not pass quantity
                    'quantity' => 1,
                ]],
            ]);

            $session->set('stripe-session-id', $stripeSession->id);

            // unset($_SESSION['stripe-session-id']); // To delete a session var

            // $session->remove('stripe-session-id');
            // $session->remove('session-data');

            $em = $this->getDoctrine()->getManager();
            $user = $this->getUser();
            $user->setStripeSessionId($stripeSession->id);
            $em->persist($user);
            $em->flush();













            return $this->redirect($stripeSession->url, 303);
        }

        // $endpoint_secret = 'whsec_Cl1FIM6uvwBE5h9BEpMOBcQsyQl5siZX';

        // $payload = @file_get_contents('php://input');
        // $sig_header = $_SERVER['HTTP_STRIPE_SIGNATURE'];
        // $event = null;

        // try {
        //     $event = \Stripe\Webhook::constructEvent(
        //         $payload,
        //         $sig_header,
        //         $endpoint_secret
        //     );
        // } catch (\UnexpectedValueException $e) {
        //     // Invalid payload
        //     http_response_code(400);
        //     exit();
        // } catch (\Stripe\Exception\SignatureVerificationException $e) {
        //     // Invalid signature
        //     http_response_code(400);
        //     exit();
        // }
        // // $id = $event->data->object->id;
        // // $captured = $event->data->object->paid;
        // // $amount = $event->data->object->amount_captured;
        // // $currency = $event->data->object->currency;
        // // $cus_email = $event->data->object->receipt_email;
        // // $name = $event->data->object->billing_details->name;

        // // Handle the event
        // switch ($event->type) {

        //     case 'invoice.payment_succeeded':



        //         $em = $this->getDoctrine()->getManager();
        //         $user = $this->getUser();
        //         $user->setPaymentStatus(true);
        //         $em->persist($user);
        //         $em->flush();

        //         break;




        //         // ... handle other event types
        //     default:
        //         echo 'Received unknown event type ' . $event->type;
        // }

        // http_response_code(200);
    }



    #[Route('/success-url', name: 'success_url')]
    public function successUrl(UserRepository $ur, $stripeAPI): Response
    {
        $this->paymentStatus($stripeAPI, $ur);
        return $this->render('payment/success.html.twig', []);
    }


    #[Route('/cancel-url', name: 'cancel_url')]
    public function cancelUrl(UserRepository $ur, $stripeAPI): Response
    {
        $this->paymentStatus($stripeAPI, $ur);

        return $this->render('payment/cancel.html.twig', []);
    }

    private function paymentStatus($stripeAPI, UserRepository $ur)
    {
        Stripe::setApiKey($stripeAPI);

        $session = $this->requestStack->getSession();

        $sd =  \Stripe\Checkout\Session::retrieve($session->get('stripe-session-id'));

        $session->set('session-data', $sd);

        $user = $ur->findOneBy(['stripe_session_id' => $session->get('stripe-session-id')]);
        if ($sd->payment_status === 'paid') {
            # code...
            $em = $this->getDoctrine()->getManager();
            $user->setPaymentStatus(\true);
            $em->persist($user);
            $em->flush();

            return $this->json(['paid' => true]);
        }

        return $this->json(['paid' => false]);


        // return $this->render('payment/check.html.twig', ['sd' => $sd->payment_status]);
    }
}
