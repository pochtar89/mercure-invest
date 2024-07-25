<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mercure\Authorization;
use Symfony\Component\Routing\Attribute\Route;

class SubscriberController extends AbstractController
{
    #[Route('/subscriber', name: 'app_subscriber')]
    public function subscriber(Request $request, Authorization $authorization): Response
    {
        return $this->render('subscriber/subscriber.html.twig', [
            'controller_name' => 'SubscriberController',
        ]);
    }

    #[Route('/subscriber-auth', name: 'app_subscriber_auth')]
    public function index(): Response
    {
        return $this->render('subscriber/subscriberAuth.html.twig', [
            'controller_name' => 'SubscriberController',
        ]);
    }
}
