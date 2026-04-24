<?php

namespace App\Controller;

use App\Entity\Beacon;
use App\Entity\Runner;
use App\Entity\ScanLog;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

#[Route('/api/beacon')]
final class BeaconController extends AbstractController
{
    /**
     * 🔹 SCAN UNIQUE (PROF + RUNNER)
     */
    #[Route('/scan/{id}', name: 'api_beacon_scan', methods: ['POST'])]
    public function scan(
        Beacon $beacon,
        Request $request,
        EntityManagerInterface $em
    ): JsonResponse {

        $data = json_decode($request->getContent(), true);

        if (!$data) {
            return new JsonResponse([
                'error' => 'Invalid JSON'
            ], 400);
        }

        $now = new \DateTimeImmutable();

        /**
         * 🧭 CAS PROF : positionnement de la balise
         */
        if (isset($data['latitude'], $data['longitude'])) {

            $beacon->setLatitude($data['latitude']);
            $beacon->setLongitude($data['longitude']);
            $beacon->setIsPlaced(true);

            $em->flush();

            return new JsonResponse([
                'status' => 'beacon_updated'
            ]);
        }

        /**
         * 🧍 CAS RUNNER : scan de passage
         */
        if (isset($data['runner_id'])) {

            $runner = $em->getRepository(Runner::class)->find($data['runner_id']);

            if (!$runner) {
                return new JsonResponse([
                    'error' => 'Runner not found'
                ], 404);
            }

            // Départ
            if ($beacon->getStatus() === 'depart' && $runner->getDateStart() === null) {
                $runner->setDateStart($now);
            }

            // Arrivée
            if ($beacon->getStatus() === 'arrivee' && $runner->getDateEnd() === null) {
                $runner->setDateEnd($now);
            }

            // Log
            $log = new ScanLog();
            $log->setIdRunner($runner);
            $log->setIdBeacon($beacon);
            $log->setScanAt($now);

            $em->persist($log);
            $em->flush();

            return new JsonResponse([
                'status' => 'scan_saved'
            ]);
        }

        return new JsonResponse([
            'error' => 'Invalid payload'
        ], 400);
    }

    /**
     * 🔹 QR CODE DATA (URL pour Flutter)
     */
    #[Route('/qr/{id}', name: 'api_beacon_qr', methods: ['GET'])]
    public function qr(
        Beacon $beacon,
        UrlGeneratorInterface $urlGenerator
    ): JsonResponse {

        $url = $urlGenerator->generate(
            'api_beacon_scan',
            ['id' => $beacon->getId()],
            UrlGeneratorInterface::ABSOLUTE_URL
        );

        // sécurité HTTPS (ngrok friendly)
        $url = str_replace('http://', 'https://', $url);

        return new JsonResponse([
            'beacon_id' => $beacon->getId(),
            'scan_url' => $url
        ]);
    }

    /**
     * 🔹 CREATE BEACON (API ONLY)
     */
    #[Route('/create', name: 'api_beacon_create', methods: ['POST'])]
    public function create(
        Request $request,
        EntityManagerInterface $em
    ): JsonResponse {

        $data = json_decode($request->getContent(), true);

        if (!$data) {
            return new JsonResponse([
                'error' => 'Invalid JSON'
            ], 400);
        }

        $beacon = new Beacon();

        $beacon->setStatus($data['type'] ?? 'balise');
        $beacon->setCreatedAt(new \DateTime());
        $beacon->setIsPlaced(false);

        $em->persist($beacon);
        $em->flush();

        return new JsonResponse([
            'status' => 'created',
            'id' => $beacon->getId()
        ]);
    }
}