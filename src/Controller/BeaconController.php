<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use App\Form\BeaconCreateType;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\Beacon;

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
        $beacons = $em->getRepository(Beacon::class)->findAll();
        return $this->render('beacon/beacon_details.html.twig', [
            'beacons' => $beacons,
        ]);
        
    }
    #[Route('/beacon/create_form', name: 'app_beacon_create')]
    public function create(Request $request, EntityManagerInterface $em): Response
    {
        $form = $this->createForm(BeaconCreateType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();
            $em->persist($data);
            $em->flush();
            return $this->redirectToRoute('app_beacon');
        }
            return $this->render('beacon/create_beacon.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
