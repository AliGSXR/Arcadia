<?php

namespace App\Controller;

use App\Entity\RapportVeterinaire;
use App\Form\RapportVeterinaireType;
use App\Repository\AnimalRepository;
use App\Repository\RapportVeterinaireRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/admin/rapport-veterinaire')]
class RapportVeterinaireController extends AbstractController
{
    #[Route('/', name: 'app_rapport_veterinaire_index', methods: ['GET'])]
    public function index(RapportVeterinaireRepository $rapportVeterinaireRepository, AnimalRepository $animalRepository, Request $request): Response
    {
        // Récupère les paramètres de filtrage depuis la requête
        $date = $request->query->get('date');
        $animalId = $request->query->get('animal');

        // Récupère la liste des animaux pour le formulaire de filtrage
        $animals = $animalRepository->findAll();

        // Filtrer les rapports en fonction du rôle de l'utilisateur
        if ($this->isGranted('ROLE_ADMIN')) {
            $rapports = $rapportVeterinaireRepository->findByCriteria($date, $animalId);
        } else {
            $rapports = $rapportVeterinaireRepository->findByUser($this->getUser());
        }

        return $this->render('admin/rapport_veterinaire/index.html.twig', [
            'rapport_veterinaires' => $rapports,
            'date' => $date,
            'animalSelectedId' => $animalId,
            'animals' => $animals,
        ]);
    }

    #[IsGranted("ROLE_VETERINARIAN")]
    #[Route('/new', name: 'app_rapport_veterinaire_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $rapportVeterinaire = new RapportVeterinaire();
        $form = $this->createForm(RapportVeterinaireType::class, $rapportVeterinaire);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $rapportVeterinaire->setDate(new \DateTime());
            $rapportVeterinaire->setUser($this->getUser());
            $entityManager->persist($rapportVeterinaire);
            $entityManager->flush();

            return $this->redirectToRoute('app_rapport_veterinaire_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('admin/rapport_veterinaire/new.html.twig', [
            'rapport_veterinaire' => $rapportVeterinaire,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_rapport_veterinaire_show', methods: ['GET'])]
    public function show(RapportVeterinaire $rapportVeterinaire): Response
    {
        if (!$this->isGranted('ROLE_ADMIN') && $this->getUser() !== $rapportVeterinaire->getUser()){
            throw $this->createNotFoundException("Vous n'êtes pas autorisé.");
        }
        return $this->render('admin/rapport_veterinaire/show.html.twig', [
            'rapport_veterinaire' => $rapportVeterinaire,
        ]);
    }

    #[IsGranted("ROLE_ADMIN")]
    #[Route('/{id}', name: 'app_rapport_veterinaire_delete', methods: ['POST'])]
    public function delete(Request $request, RapportVeterinaire $rapportVeterinaire, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$rapportVeterinaire->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($rapportVeterinaire);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_rapport_veterinaire_index', [], Response::HTTP_SEE_OTHER);
    }
}
