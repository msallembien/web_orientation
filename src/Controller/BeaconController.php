<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use App\Form\BeaconCreateType;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\Beacon;
use App\Entity\ScanLog;
use App\Entity\Runner;
use Symfony\Component\HttpFoundation\JsonResponse;
use Endroid\QrCode\Builder\Builder;
use Endroid\QrCode\Writer\PngWriter;
use Endroid\QrCode\Writer\SvgWriter;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

final class BeaconController extends AbstractController
{
    #[Route('/beacon', name: 'app_beacon')]
    public function index(EntityManagerInterface $em): Response
    {
        $beacons = $em->getRepository(Beacon::class)->findAll();

        return $this->render('beacon/index.html.twig', [
            'beacons' => $beacons,
        ]);
    }
    #[Route('/beacon/beacon_details/{id}', name: 'app_beacon_details')]
    public function details(Beacon $beacon, EntityManagerInterface $em): Response
    {
    $dataUrl = $this->generateUrl(
        'app_beacon_scan',
        ['id' => $beacon->getId()],
        UrlGeneratorInterface::ABSOLUTE_URL
    );

    // S’assurer que c’est HTTPS
    $dataUrl = preg_replace('#^http:#', 'https:', $dataUrl);

    $builder = new Builder(
        writer: new SvgWriter(),
        writerOptions: [],
        validateResult: false,
        data: $dataUrl,
        size: 200,
        margin: 10
    );

    $result = $builder->build();

    // 🔹 Stocker le QR code dans l'entité
    $beacon->setQrCode($dataUrl);
    $em->flush();

    return $this->render('beacon/beacon_details.html.twig', [
        'beacon' => $beacon,
        'qrCode' => $result->getDataUri(),
    ]);
    }
    #[Route('/beacon/scan/{id}', name: 'app_beacon_scan', methods: ['POST'])]
    public function scan(
        Beacon $beacon,
        Request $request,
        EntityManagerInterface $em
    ): JsonResponse {
        $data = json_decode($request->getContent(), true);

        // 🔹 CAS 1 : PROF (il envoie latitude + longitude)
        if (isset($data['latitude'], $data['longitude'])) {
            $beacon->setLatitude($data['latitude']);
            $beacon->setLongitude($data['longitude']);
            $beacon->setIsPlaced(true);

            $em->flush();

            return new JsonResponse(['status' => 'beacon_updated']);
        }

        // 🔹 CAS 2 : ÉLÈVE (il envoie runner_id)
        if (isset($data['runner_id'])) {
            $runner = $em->getRepository(Runner::class)->find($data['runner_id']);

            if (!$runner) {
                return new JsonResponse(['error' => 'Runner not found'], 404);
            }

            $scanLog = new ScanLog();
            $scanLog->setIdRunner($runner);
            $scanLog->setIdBeacon($beacon);
            $scanLog->setScanAt(new \DateTimeImmutable());

            $em->persist($scanLog);
            $em->flush();

            return new JsonResponse(['status' => 'scan_saved']);
        }

        return new JsonResponse(['error' => 'Invalid data'], 400);
    }
    #[Route('/beacon/scan/save/{id}', name: 'app_beacon_scan_save', methods: ['POST'])]
    public function saveScan(
        Beacon $beacon,
        Request $request,
        EntityManagerInterface $em
    ): Response {
       $data = json_decode($request->getContent(), true);

        if (!$data || !isset($data['latitude'], $data['longitude'])) {
            return new Response('Invalid data', 400);
        }

        $beacon->setLatitude($data['latitude']);
        $beacon->setLongitude($data['longitude']);
        $beacon->setIsPlaced(true);

        $em->flush();

        return new Response('OK');
    }
    #[Route('/api/scan_logs', name: 'api_scan_logs', methods: ['POST'])]
    public function apiScanLogs(Request $request, EntityManagerInterface $em): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        if (!$data || !isset($data['runner_id'], $data['beacon_id'])) {
            return new JsonResponse(['error' => 'Invalid data'], 400);
        }

        $runner = $em->getRepository(Runner::class)->find($data['runner_id']);
        $beacon = $em->getRepository(Beacon::class)->find($data['beacon_id']);

        if (!$runner || !$beacon) {
            return new JsonResponse(['error' => 'Runner or Beacon not found'], 404);
        }

        // 🔹 Date actuelle
        $now = new \DateTimeImmutable();

        // 🔹 Vérifie le type de balise
        if ($beacon->getStatus() === 'depart' && $runner->getDateStart() === null) {
            $runner->setDateStart($now);
        } elseif ($beacon->getStatus() === 'arrivee' && $runner->getDateEnd() === null) {
            $runner->setDateEnd($now);
        }

        // 🔹 Enregistre le scan log
        $scanLog = new ScanLog();
        $scanLog->setIdRunner($runner);
        $scanLog->setIdBeacon($beacon);
        $scanLog->setScanAt($now);

        $em->persist($scanLog);
        $em->flush();

        return new JsonResponse([
            'status' => 'ok',
            'date_start' => $runner->getDateStart(),
            'date_end' => $runner->getDateEnd()
        ]);
    }
    #[Route('/beacon/create_form', name: 'app_beacon_create')]
    public function create(Request $request, EntityManagerInterface $em): Response
    {
        $form = $this->createForm(BeaconCreateType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $beacon = $form->getData();
            $type = $form->get('type')->getData();
            $beacon->setstatus($type);
           
            if ($type === 'depart') {
                $count = $em->createQueryBuilder()
                ->select('COUNT(b.id)')
                ->from(Beacon::class, 'b')
                ->where('b.name LIKE :name')
                ->setParameter('name', 'depart%')
                ->getQuery()
                ->getSingleScalarResult();

                $beacon->setName('depart' . ($count + 1));
            } elseif ($type === 'arrivee') {
                $count = $em->createQueryBuilder()
                    ->select('COUNT(b.id)')
                    ->from(Beacon::class, 'b')
                    ->where('b.name LIKE :name')
                    ->setParameter('name', 'arrivee%')
                    ->getQuery()
                    ->getSingleScalarResult();
                $beacon->setName('arrivee' . ($count + 1));
            } else {
                $beacon->setName('balise_' . uniqid());
            }

            // ✅ Date automatique
            $beacon->setCreatedAt(new \DateTime());
            
            // Optionnel
            $beacon->setIsPlaced(false);

            $em->persist($beacon);
            $em->flush();

            return $this->redirectToRoute('app_beacon');
        }
        return $this->render('beacon/create_beacon.html.twig', [
        'form' => $form->createView(),
        ]);
    }
}
