<?php

namespace App\Controller;

use App\Entity\Beacon;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

class BeaconApiController extends AbstractController
{
    #[Route('/api/beacon_scan/{id}', name: 'api_beacon_scan', methods: ['GET'])]
    public function scan(Beacon $beacon): JsonResponse
    {
        return $this->json([
            'id' => $beacon->getId(),
            'status' => $beacon->getStatus(),
            'name' => $beacon->getName(),
        ]);
    }
}