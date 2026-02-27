<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class BeaconController extends AbstractController
{
    #[Route('/beacon', name: 'app_beacon')]
    public function index(): Response
    {
        return $this->render('beacon/index.html.twig', [
            'controller_name' => 'BeaconController',
        ]);
    }

}
