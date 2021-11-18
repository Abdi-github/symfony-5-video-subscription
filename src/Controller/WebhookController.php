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
        Stripe::setApiKey($stripeAPI);
        $endpoint_secret = 'whsec_Cl1FIM6uvwBE5h9BEpMOBcQsyQl5siZX';



        $session = $this->requestStack->getSession();
        $session->set('Req', $request);
    }
}
