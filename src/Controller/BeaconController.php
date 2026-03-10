<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use App\Form\BeaconCreateType;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\ORM\EntityManagerInterface;

final class BeaconController extends AbstractController
{
    #[Route('/beacon', name: 'app_beacon')]
    public function index(): Response
    {
        return $this->render('beacon/index.html.twig', [
            'controller_name' => 'BeaconController',
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
