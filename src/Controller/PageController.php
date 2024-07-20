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
}
