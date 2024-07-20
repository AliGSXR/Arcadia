<?php

namespace App\Controller;

use App\Entity\Image;
use App\Form\ImageType;
use App\Repository\ImageRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/admin/image')]
class ImageController extends AbstractController
{
    #[Route('/', name: 'app_image_index', methods: ['GET'])]
    public function index(ImageRepository $imageRepository): Response
    {
        return $this->render('admin/image/index.html.twig', [
            'images' => $imageRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_image_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $image = new Image();
        $form = $this->createForm(ImageType::class, $image);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Récupérer le fichier téléchargé
            $file = $form->get('image')->getData();

            if ($file) {
                // Assurez-vous que $file est une instance de UploadedFile
                if ($file instanceof \Symfony\Component\HttpFoundation\File\UploadedFile) {
                    // Lire le contenu du fichier
                    $imageData = file_get_contents($file->getPathname());

                    // Créer une nouvelle entité Image
                    $image = new Image();
                    $image->setData($imageData);
                    $image->setName($form->get('name')->getData());
                    // Sauvegarder l'image dans la base de données
                    $entityManager->persist($image);
                    $entityManager->flush();

                    return $this->redirectToRoute('app_image_index', [], Response::HTTP_SEE_OTHER);
                } else {
                    // Gérer le cas où le fichier n'est pas valide
                    throw new \Exception('Le fichier téléchargé n\'est pas valide.');
                }
            }
        }

        return $this->render('admin/image/new.html.twig', [
            'image' => $image,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_image_show', methods: ['GET'])]
    public function show(Image $image): Response
    {
        return $this->render('admin/image/show.html.twig', [
            'image' => $image,
        ]);
    }

    #[Route('/image/{id}', name: 'app_get_image')]
    public function getImage(Image $image): Response
    {
        if (!$image) {
            throw $this->createNotFoundException('Image not found');
        }

        // Récupérer les données de l'image
        $imageData = $image->getData();

        // Vérifier si $imageData est une ressource
        if (is_resource($imageData)) {
            // Convertir la ressource en chaîne de caractères
            $imageData = stream_get_contents($imageData);
        }

        // Déterminer le type MIME
        $mimeType = $this->detectMimeType($imageData);

        // Créer une réponse avec les données de l'image
        $response = new Response($imageData);
        $response->headers->set('Content-Type', $mimeType);

        return $response;
    }

    private function detectMimeType(string $data): string
    {
        $finfo = new \finfo(FILEINFO_MIME_TYPE);
        return $finfo->buffer($data);
    }

    #[Route('/{id}', name: 'app_image_delete', methods: ['POST'])]
    public function delete(Request $request, Image $image, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$image->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($image);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_image_index', [], Response::HTTP_SEE_OTHER);
    }
}
