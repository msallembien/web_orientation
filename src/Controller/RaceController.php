<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\Race;
use Symfony\UX\Map\Map;


final class RaceController extends AbstractController
{
    #[Route('/race', name: 'app_race')]
    public function index(EntityManagerInterface $em): Response
    {
        $race = $em->getRepository(Race::class)->findAll();

        return $this->render('race/index.html.twig', [
            'races' => $race,
        ]);
    }
    #[Route('/race/details_race/{id}', name: 'app_race_details')]
    public function details(Race $race): Response
    { 
        $maps = new Map();
        return $this->render('race/details_race.html.twig', [
            'my_map' => $maps,
        ]);
    }
}
