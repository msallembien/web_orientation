<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\ORM\EntityManagerInterface;

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
    #[Route('/map/details_parcours/{id}', name: 'app_map_details')]
    public function details(mapEntity $map): Response
    { 
        $maps = new Map();
        $ready = 0;
        $r = false;
        foreach ($map->getBeacons() as $beacon) {
            if ($beacon->isPlaced() === true) {
                $ready++;
            }
        }
        if ($ready === count($map->getBeacons())) {
            $r = true;
        }
        return $this->render('parcours/details_parcours.html.twig', [
            'map' => $map,
            'my_map' => $maps,
            'beacons' => $map->getBeacons(),
            'ready' => $r,
        ]);
        
    }
    #[Route('/parcours/create_form', name: 'app_parcours_create')]
    public function create(Request $request, EntityManagerInterface $em): Response
    {
        $form = $this->createForm(ParcoursCreateType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();
            $em->persist($data);
            $em->flush();
            return $this->redirectToRoute('app_parcours');
        }
        return $this->render('parcours/create_parcours.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
