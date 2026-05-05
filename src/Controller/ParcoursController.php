<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\ORM\EntityManagerInterface;
use Endroid\QrCode\Builder\Builder;
use Endroid\QrCode\Writer\SvgWriter;
use Endroid\QrCode\QrCode;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;

use App\Entity\Map as mapEntity;
use Symfony\UX\Map\Map;
use App\Form\ParcoursCreateType;
use App\Entity\Beacon;


final class ParcoursController extends AbstractController
{
    #[Route('/parcours', name: 'app_parcours')]
    public function index(EntityManagerInterface $em): Response
    {
        $map = $em->getRepository(mapEntity::class)->findAll();

        return $this->render('parcours/index.html.twig', [
            'maps' => $map,
        ]);
    }
    #[Route('/parcours/details_parcours/{id}', name: 'app_map_details')]
    public function details(mapEntity $map): Response
    {
        $ready = 0;
        $r = false;

        $markers = [];
        $path = [];

        foreach ($map->getBeacons() as $beacon) {

            if ($beacon->isPlaced()) {
                $ready++;
            }

            if ($beacon->getLatitude() !== null && $beacon->getLongitude() !== null) {

                // 🔹 Marker
                $markers[] = [
                    'lat' => (float) $beacon->getLatitude(),
                    'lng' => (float) $beacon->getLongitude(),
                    'title' => $beacon->getName() ?? 'Sans nom',
                    'type' => $beacon->getStatus() ?: 'balise'
                ];

                $path[] = [
                    'lat' => (float) $beacon->getLatitude(),
                    'lng' => (float) $beacon->getLongitude()
                ];
            }
        }

        if ($ready === count($map->getBeacons())) {
            $r = true;
        }

        return $this->render('parcours/details_parcours.html.twig', [
            'map' => $map,
            'beacons' => $map->getBeacons(),
            'ready' => $r,
            'markers' => $markers,
            'path' => $path,
            'google_maps_api_key' => $_ENV['GOOGLE_MAPS_API_KEY']
        ]);
    }
    #[Route('/parcours/create_form', name: 'app_parcours_create')]
    public function create(Request $request, EntityManagerInterface $em): Response
    {
        $map = new mapEntity();

        $form = $this->createForm(ParcoursCreateType::class, $map);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
        $map->setCreatedAt(new \DateTime());
        $map->setStatus('draft');
        $count = $request->request->get('beacon_count', 1);

        // Départ
        $start = new Beacon();
        $start->setStatus('depart');
        $start->setName('depart');
        $start->setIdMap($map);
        $start->setCreatedAt(new \DateTime());
        $start->setIsPlaced(false);
        $map->addBeacon($start);

        // Balises intermédiaires
        for ($i = 0; $i < $count; $i++) {
            $b = new Beacon();
            $b->setStatus('normal');
            $b->setName('balise_' . ($i + 1));
            $b->setIdMap($map);
            $b->setCreatedAt(new \DateTime());
            $b->setIsPlaced(false);
            $map->addBeacon($b);
        }

        // Arrivée
        $end = new Beacon();
        $end->setStatus('arrivee');
        $end->setName('arrivee');
        $end->setIdMap($map);
        $end->setCreatedAt(new \DateTime());
        $end->setIsPlaced(false);
        $map->addBeacon($end);

            $em->persist($map);
            $em->flush();

            return $this->redirectToRoute('app_parcours');
        }

        return $this->render('parcours/create_parcours.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/parcours/{id}/edit', name: 'app_parcours_edit', methods: ['GET', 'POST'])]
    public function edit(mapEntity $map, Request $request, EntityManagerInterface $em): Response
    {
        $form = $this->createForm(ParcoursCreateType::class, $map);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->flush();

            return $this->redirectToRoute('app_parcours');
        }

        return $this->render('parcours/edit_parcours.html.twig', [
            'form' => $form->createView(),
            'map' => $map,
        ]);
    }

    #[Route('/parcours/{id}/delete', name: 'app_parcours_delete', methods: ['POST'])]
    public function delete(mapEntity $map, Request $request, EntityManagerInterface $em): RedirectResponse
    {
        if (!$this->isCsrfTokenValid('delete_map_' . $map->getId(), (string) $request->request->get('_token'))) {
            return $this->redirectToRoute('app_parcours');
        }

        if ($map->getRaces()->count() > 0) {
            return $this->redirectToRoute('app_parcours');
        }

        foreach ($map->getBeacons() as $beacon) {
            $em->remove($beacon);
        }

        $em->remove($map);
        $em->flush();

        return $this->redirectToRoute('app_parcours');
    }
    #[Route('/parcours/{id}/ready', name: 'app_map_ready', methods: ['POST'])]
    public function setReady(mapEntity $map, EntityManagerInterface $em): Response
    {
        $map->setStatus('Ready');

        $em->persist($map);
        $em->flush();

        return $this->redirectToRoute('app_map_details', [
            'id' => $map->getId()
        ]);
    }
    #[Route('/parcours/{id}/qrcodes', name: 'qr_code_parcours')]
    public function qrcodes(mapEntity $map, EntityManagerInterface $em): Response
    {
        $qrCodes = [];

        foreach ($map->getBeacons() as $beacon) {

            $url = $this->generateUrl(
                'api_beacon_scan',
                ['id' => $beacon->getId()],
                UrlGeneratorInterface::ABSOLUTE_URL
            );

        // 🔥 force https
        $url = str_replace('http://', 'https://', $url);

            $builder = new Builder(
                writer: new SvgWriter(),
                writerOptions: [],
                validateResult: false,
                data: $url,
                size: 300,
                margin: 10
            );

            $result = $builder->build();

            $qrCodes[] = [
                'beacon' => $beacon,
                'qr' => $result->getDataUri(),
            ];
        }

        return $this->render('parcours/qr_codes_parcours.html.twig', [
            'map' => $map,
            'qrCodes' => $qrCodes,
        ]);
    }
}
