<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\Map as mapEntity;
use Symfony\UX\Map\Map;


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
        return $this->render('parcours/details_parcours.html.twig', [
            'map' => $map,
            'my_map' => $maps,
        ]);
        
    }
}
