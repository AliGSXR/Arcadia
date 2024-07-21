<?php

namespace App\Controller;

use App\Repository\AvisRepository;
use App\Repository\HabitatRepository;
use App\Repository\ServiceRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class PageController extends AbstractController
{
    public function __construct(
        private readonly AvisRepository $avisRepository,
        private readonly ServiceRepository $serviceRepository,
        private readonly HabitatRepository $habitatRepository,
    )
    {
    }

    #[Route('/', name: 'app_home')]
    public function index(): Response
    {
        $services = $this->serviceRepository->findAll();
        $habitats = $this->habitatRepository->findAll();
        $avis = $this->avisRepository->findByVisible(true);
        return $this->render('page/index.html.twig', [
            'avis' => $avis,
            'services' => $services,
            'habitats' => $habitats,
        ]);
    }
    #[Route('/services', name: 'app_services')]
    public function services(): Response
    {
        return $this->render('page/services.html.twig');
    }

    #[Route('/savane', name: 'app_savane')]
    public function savane(): Response
    {
        return $this->render('page/savane.html.twig');
    }

    #[Route('/jungle', name: 'app_jungle')]
    public function jungle(): Response
    {
        return $this->render('page/jungle.html.twig');
    }

    #[Route('/marais', name: 'app_marais')]
    public function marais(): Response
    {
        return $this->render('page/marais.html.twig');
    }

    #[Route('/energies', name: 'app_energies')]
    public function energies(): Response
    {
        return $this->render('page/energies.html.twig');
    }
}
