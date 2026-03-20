<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use App\Form\BeaconCreateType;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\Beacon;
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
    public function details(Beacon $beacon): Response
    {
        $builder = new Builder(
            writer: new SvgWriter(),
            writerOptions: [],
            validateResult: false,
            data: $this->generateUrl(
                'app_beacon_scan',
                ['id' => $beacon->getId()],
                UrlGeneratorInterface::ABSOLUTE_URL
            ),
            size: 200,
            margin: 10
        );

        $result = $builder->build();

        return $this->render('beacon/beacon_details.html.twig', [
            'beacon' => $beacon,
            'qrCode' => $result->getDataUri(),
        ]);
    }
    #[Route('/beacon/scan/{id}', name: 'app_beacon_scan')]
    public function scan(Beacon $beacon): Response
    {
        return $this->render('beacon/scan.html.twig', [
            'beacon' => $beacon,
        ]);
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
        $beacon->setIsPlaced(true);

        $em->flush();

        return new Response('OK');
    }
    #[Route('/beacon/create_form', name: 'app_beacon_create')]
    public function create(Request $request, EntityManagerInterface $em): Response
    {
        $form = $this->createForm(BeaconCreateType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $beacon = $form->getData();
            $type = $form->get('type')->getData();

            // 🔥 Compter les balises existantes du même type
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
