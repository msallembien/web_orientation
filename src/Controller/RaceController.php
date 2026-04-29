<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\Map as mapEntity;
use Symfony\UX\Map\Map;
use App\Entity\Runner;
use App\Entity\ScanLog;
use App\Entity\Race;


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
    public function details(Race $race, EntityManagerInterface $em): Response
    {
        $runners = $em->getRepository(Runner::class)->findBy([
            'id_race' => $race
        ]);

        $results = [];

        foreach ($runners as $runner) {
            $start = $runner->getDateStart();
            $end = $runner->getDateEnd();

            $time = null;

            if ($start && $end) {
                $time = $end->getTimestamp() - $start->getTimestamp();
            }

            $results[] = [
                'runner' => $runner,
                'time' => $time
            ];
        }

        // 🏆 tri du plus rapide au plus lent
        usort($results, function ($a, $b) {
            return $a['time'] <=> $b['time'];
        });

        return $this->render('race/details_race.html.twig', [
            'results' => $results,
            'race' => $race
        ]);
    }
    #[Route('/runner/details/{id}', name: 'app_runner_details')]
    public function runnerDetails(Runner $runner, EntityManagerInterface $em): Response
    {
        $logs = $em->getRepository(ScanLog::class)->findBy(
            ['id_runner' => $runner],
            ['scan_at' => 'ASC']
        );

        $logsData = [];
        $path = array_map(function ($log) {
            return [
                'lat' => (float) $log->getLatitude(),
                'lng' => (float) $log->getLongitude(),
            ];
        }, $logs);
        foreach ($logs as $log) {

            $logsData[] = [
                'lat' => (float) $log->getLatitude(),
                'lng' => (float) $log->getLongitude(),
                'isBeacon' => $log->getIdBeacon() !== null,
                'status' => $log->getIdBeacon()?->getStatus(), // depart/arrivee si existe
            ];
        }

        return $this->render('race/runner_details.html.twig', [
            'runner' => $runner,
            'path' => $path,
            'logs' => $logsData,
            'google_maps_api_key' => $_ENV['GOOGLE_MAPS_API_KEY']
        ]);
    }


    #[Route('/race/create', name: 'app_race_create', methods: ['GET', 'POST'])]
    public function create(Request $request, EntityManagerInterface $em): Response
    {
        // 🔍 récupérer uniquement les parcours READY
        $maps = $em->getRepository(mapEntity::class)->findBy([
            'status' => 'READY'
        ]);

        if ($request->isMethod('POST')) {

            $mapId = $request->request->get('map_id');

            $map = $em->getRepository(mapEntity::class)->find($mapId);

            if (!$map) {
                throw $this->createNotFoundException("Parcours introuvable");
            }

            // 🎲 génération code aléatoire (6 lettres MAJ)
            $code = strtoupper(substr(bin2hex(random_bytes(3)), 0, 6));

            $race = new Race();
            $race->setRaceName('Course ' . date('d/m H:i'));
            $race->setIdMap($map);
            $race->setNbRunner(0);
            $race->setCodeRace($code);
            $race->setStatus('CREATED');

            $em->persist($race);
            $em->flush();

            return $this->redirectToRoute('app_race');
        }

        return $this->render('race/create.html.twig', [
            'maps' => $maps
        ]);
    }
}
